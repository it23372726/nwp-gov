<?php
/**
 * Hybrid LLM intent picker — selects a knowledge-base intent id only.
 * Never returns free-form government answers.
 */
header('Content-Type: application/json; charset=utf-8');

const RATE_LIMIT_FILE = __DIR__ . '/.match_intent_rate';
const MAX_REQUESTS_PER_HOUR = 40;
const MAX_QUESTION_LENGTH = 500;

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function textLength(string $text): int
{
    return function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
}

function truncateText(string $text, int $max): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($text, 0, $max);
    }
    return substr($text, 0, $max);
}

function sanitizeQuestion(string $question): string
{
    $question = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $question);
    $question = preg_replace('/\s+/u', ' ', $question);
    return trim(truncateText($question, MAX_QUESTION_LENGTH));
}

function getClientKey(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return hash('sha256', $ip);
}

function isRateLimited(string $clientKey): bool
{
    $now = time();
    $limits = [];

    if (is_file(RATE_LIMIT_FILE)) {
        $raw = file_get_contents(RATE_LIMIT_FILE);
        $limits = json_decode($raw, true) ?: [];
    }

    $limits = array_filter($limits, static fn($entry) => ($entry['expires'] ?? 0) > $now);

    if (!isset($limits[$clientKey])) {
        $limits[$clientKey] = ['count' => 0, 'expires' => $now + 3600];
    }

    if ($limits[$clientKey]['count'] >= MAX_REQUESTS_PER_HOUR) {
        file_put_contents(RATE_LIMIT_FILE, json_encode($limits), LOCK_EX);
        return true;
    }

    $limits[$clientKey]['count']++;
    file_put_contents(RATE_LIMIT_FILE, json_encode($limits), LOCK_EX);
    return false;
}

function loadLlmConfig(): ?array
{
    $local = __DIR__ . '/chatbot-config.local.php';
    if (!is_file($local)) {
        return null;
    }
    $cfg = include $local;
    if (!is_array($cfg)) {
        return null;
    }
    $key = trim((string) ($cfg['OPENAI_API_KEY'] ?? ''));
    if ($key === '' || str_starts_with($key, 'sk-your-key')) {
        return null;
    }
    return [
        'api_key' => $key,
        'base_url' => rtrim((string) ($cfg['OPENAI_BASE_URL'] ?? 'https://api.openai.com/v1'), '/'),
        'model' => (string) ($cfg['OPENAI_MODEL'] ?? 'gpt-4o-mini'),
        'timeout' => (int) ($cfg['OPENAI_TIMEOUT'] ?? 12),
    ];
}

function buildCatalog(array $knowledgeBase, array $candidateIds): array
{
    $byId = [];
    foreach ($knowledgeBase as $item) {
        if (!isset($item['id'])) {
            continue;
        }
        $byId[$item['id']] = $item;
    }

    $ids = $candidateIds;
    if (!$ids) {
        $ids = array_keys($byId);
    }

    $catalog = [];
    foreach ($ids as $id) {
        if (!isset($byId[$id])) {
            continue;
        }
        $item = $byId[$id];
        $keywords = array_slice($item['keywords'] ?? [], 0, 8);
        $hint = $item['suggestions']['en']
            ?? $item['suggestions']['si']
            ?? ($keywords[0] ?? $id);
        $catalog[] = [
            'id' => $id,
            'hint' => $hint,
            'keywords' => $keywords,
        ];
    }
    return $catalog;
}

function callOpenAiCompatible(array $cfg, string $question, string $lang, array $catalog): ?string
{
    $allowed = array_column($catalog, 'id');
    $catalogJson = json_encode($catalog, JSON_UNESCAPED_UNICODE);

    $system = <<<'PROMPT'
You are an intent classifier for a North Western Provincial Council (Sri Lanka) citizen chatbot.
Pick the single best knowledge-base intent id for the user question, or null if none fit.
Rules:
- Return ONLY valid JSON: {"intent_id":"<id>"|null}
- intent_id MUST be one of the allowed catalog ids, or null
- Do not invent answers, explanations, or ids outside the catalog
- Prefer the most specific service intent over a generic *_about when the question is about a service
- Questions may be in Sinhala, English, or Tamil; spelling may be imperfect
PROMPT;

    $user = "UI language hint: {$lang}\nQuestion: {$question}\nAllowed intents catalog:\n{$catalogJson}";

    $payload = [
        'model' => $cfg['model'],
        'temperature' => 0,
        'response_format' => ['type' => 'json_object'],
        'messages' => [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ],
    ];

    $url = $cfg['base_url'] . '/chat/completions';
    $body = json_encode($payload);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $cfg['api_key'],
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => max(3, $cfg['timeout']),
        ]);
        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($errno || $status < 200 || $status >= 300 || !is_string($raw)) {
            return null;
        }
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAuthorization: Bearer {$cfg['api_key']}\r\n",
                'content' => $body,
                'timeout' => max(3, $cfg['timeout']),
                'ignore_errors' => true,
            ],
        ]);
        $raw = @file_get_contents($url, false, $context);
        if (!is_string($raw) || $raw === '') {
            return null;
        }
    }

    $decoded = json_decode($raw, true);
    $content = $decoded['choices'][0]['message']['content'] ?? null;
    if (!is_string($content) || $content === '') {
        return null;
    }

    $parsed = json_decode($content, true);
    if (!is_array($parsed)) {
        return null;
    }

    $intentId = $parsed['intent_id'] ?? null;
    if ($intentId === null || $intentId === '' || $intentId === 'null') {
        return null;
    }
    $intentId = (string) $intentId;
    if (!in_array($intentId, $allowed, true)) {
        return null;
    }
    return $intentId;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $cfg = loadLlmConfig();
    respond(200, [
        'ok' => true,
        'llm_enabled' => $cfg !== null,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

$cfg = loadLlmConfig();
if ($cfg === null) {
    respond(200, ['ok' => false, 'reason' => 'llm_disabled']);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || !isset($data['question'])) {
    respond(400, ['ok' => false, 'error' => 'invalid_payload']);
}

$question = sanitizeQuestion((string) $data['question']);
if (textLength($question) < 2) {
    respond(400, ['ok' => false, 'error' => 'question_too_short']);
}

$lang = preg_replace('/[^a-z]/', '', strtolower((string) ($data['lang'] ?? 'en')));
if ($lang === '') {
    $lang = 'en';
}

if (isRateLimited(getClientKey())) {
    respond(429, ['ok' => false, 'error' => 'rate_limited']);
}

require __DIR__ . '/chatbot-knowledge.php';
if (!isset($knowledgeBase) || !is_array($knowledgeBase)) {
    respond(500, ['ok' => false, 'error' => 'kb_unavailable']);
}

$candidateIds = [];
if (!empty($data['candidates']) && is_array($data['candidates'])) {
    foreach ($data['candidates'] as $c) {
        if (is_array($c) && !empty($c['id'])) {
            $candidateIds[] = (string) $c['id'];
        } elseif (is_string($c)) {
            $candidateIds[] = $c;
        }
    }
    $candidateIds = array_values(array_unique($candidateIds));
}

$catalog = buildCatalog($knowledgeBase, $candidateIds);
if (!$catalog) {
    respond(200, ['ok' => true, 'intent_id' => null, 'source' => 'llm']);
}

$intentId = callOpenAiCompatible($cfg, $question, $lang, $catalog);
respond(200, [
    'ok' => true,
    'intent_id' => $intentId,
    'source' => 'llm',
]);
