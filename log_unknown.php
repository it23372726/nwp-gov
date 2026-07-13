<?php
header('Content-Type: application/json; charset=utf-8');

const LOG_FILE = __DIR__ . '/unknown_questions.log';
const RATE_LIMIT_FILE = __DIR__ . '/.unknown_log_rate';
const MAX_LOG_SIZE = 2097152; // 2 MB
const MAX_QUESTIONS_PER_HOUR = 40;
const MAX_QUESTION_LENGTH = 500;
const MAX_TOP_CANDIDATES = 5;

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

function sanitizeLang(string $lang): string
{
    $lang = preg_replace('/[^a-z]/', '', strtolower($lang));
    return in_array($lang, ['si', 'en', 'ta'], true) ? $lang : 'unknown';
}

function sanitizeDepartment($value): ?array
{
    if (!is_array($value)) {
        return null;
    }
    $id = preg_replace('/[^a-z0-9_\-]/i', '', (string) ($value['id'] ?? ''));
    $name = sanitizeQuestion((string) ($value['name'] ?? ''));
    if ($id === '' && $name === '') {
        return null;
    }
    return [
        'id' => $id !== '' ? $id : null,
        'name' => $name !== '' ? truncateText($name, 80) : null,
    ];
}

function sanitizeCandidates($candidates): array
{
    if (!is_array($candidates)) {
        return [];
    }
    $out = [];
    foreach (array_slice($candidates, 0, MAX_TOP_CANDIDATES) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $id = preg_replace('/[^a-z0-9_]/i', '', (string) ($row['id'] ?? ''));
        if ($id === '') {
            continue;
        }
        $score = isset($row['score']) ? (float) $row['score'] : null;
        $out[] = [
            'id' => $id,
            'score' => $score !== null ? round($score, 4) : null,
        ];
    }
    return $out;
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

    if ($limits[$clientKey]['count'] >= MAX_QUESTIONS_PER_HOUR) {
        file_put_contents(RATE_LIMIT_FILE, json_encode($limits), LOCK_EX);
        return true;
    }

    $limits[$clientKey]['count']++;
    file_put_contents(RATE_LIMIT_FILE, json_encode($limits), LOCK_EX);
    return false;
}

function rotateLogIfNeeded(): void
{
    if (is_file(LOG_FILE) && filesize(LOG_FILE) > MAX_LOG_SIZE) {
        $archive = LOG_FILE . '.' . date('Y-m-d_His') . '.bak';
        @rename(LOG_FILE, $archive);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || !isset($data['question'])) {
    respond(400, ['ok' => false, 'error' => 'invalid_payload']);
}

$question = sanitizeQuestion((string) $data['question']);
if (textLength($question) < 4) {
    respond(400, ['ok' => false, 'error' => 'question_too_short']);
}

$lang = sanitizeLang((string) ($data['lang'] ?? 'unknown'));
$department = sanitizeDepartment($data['department'] ?? null);
$candidates = sanitizeCandidates($data['candidates'] ?? []);
$score = isset($data['score']) ? round((float) $data['score'], 4) : null;
$secondScore = isset($data['second_score']) ? round((float) $data['second_score'], 4) : null;
$confident = isset($data['confident']) ? (bool) $data['confident'] : null;
$softMatch = isset($data['soft_match']) ? (bool) $data['soft_match'] : null;
$llmTried = isset($data['llm_tried']) ? (bool) $data['llm_tried'] : null;
$reason = preg_replace('/[^a-z0-9_\-]/i', '', (string) ($data['reason'] ?? 'no_match'));
if ($reason === '') {
    $reason = 'no_match';
}

if (isRateLimited(getClientKey())) {
    respond(429, ['ok' => false, 'error' => 'rate_limited']);
}

rotateLogIfNeeded();

$entry = [
    'ts' => date('c'),
    'lang' => $lang,
    'question' => $question,
    'reason' => $reason,
    'department' => $department,
    'match' => [
        'score' => $score,
        'second_score' => $secondScore,
        'confident' => $confident,
        'soft_match' => $softMatch,
        'llm_tried' => $llmTried,
        'top' => $candidates,
    ],
];

$line = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
$written = file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);

if ($written === false) {
    respond(500, ['ok' => false, 'error' => 'write_failed']);
}

respond(200, ['ok' => true]);
