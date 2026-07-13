<?php
/**
 * NWPC Chatbot Widget — embeddable partial
 * Requires: $t (translations), $lang, $jsonKnowledge
 */
?>
<div id="nwpcChatBackdrop" class="nwpc-chat-backdrop" aria-hidden="true"></div>

<div class="nwpc-chat-launcher-wrap">
    <button
        id="nwpcChatLauncher"
        type="button"
        class="nwpc-chat-launcher"
        aria-expanded="false"
        aria-controls="nwpcChatPanel"
        aria-label="<?php echo htmlspecialchars($t['bot_open'], ENT_QUOTES, 'UTF-8'); ?>"
    >
        <span class="nwpc-chat-launcher__icon" aria-hidden="true">
            <i class="fas fa-comments"></i>
        </span>
        <span class="nwpc-chat-launcher__label"><?php echo $t['bot_open']; ?></span>
    </button>
</div>

<div
    id="nwpcChatPanel"
    class="nwpc-chat-panel"
    role="dialog"
    aria-modal="true"
    aria-labelledby="nwpcChatTitle"
    aria-hidden="true"
>
    <header class="nwpc-chat-header">
        <div class="nwpc-chat-header__row">
            <div class="nwpc-chat-header__identity">
                <div class="nwpc-chat-header__avatar" aria-hidden="true">
                    <i class="fas fa-landmark"></i>
                </div>
                <div>
                    <h2 id="nwpcChatTitle" class="nwpc-chat-header__title"><?php echo $t['bot_title']; ?></h2>
                    <div class="nwpc-chat-header__status">
                        <span class="nwpc-chat-header__status-dot" aria-hidden="true"></span>
                        <span><?php echo $t['bot_status']; ?></span>
                    </div>
                </div>
            </div>
            <div class="nwpc-chat-header__actions">
                <button
                    type="button"
                    id="nwpcChatReset"
                    class="nwpc-chat-header__btn"
                    title="<?php echo htmlspecialchars($t['start_new_chat'], ENT_QUOTES, 'UTF-8'); ?>"
                    aria-label="<?php echo htmlspecialchars($t['start_new_chat'], ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <i class="fas fa-rotate-right" aria-hidden="true"></i>
                </button>
                <button
                    type="button"
                    id="nwpcChatClose"
                    class="nwpc-chat-header__btn"
                    aria-label="<?php echo htmlspecialchars($t['close_chat'], ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </header>

    <div id="nwpcChatMessages" class="nwpc-chat-messages" role="log" aria-live="polite" aria-relevant="additions"></div>

    <div class="nwpc-chat-helpbar">
        <span class="nwpc-chat-helpbar__label"><?php echo $t['need_help']; ?></span>
        <a href="tel:0372237169" class="nwpc-chat-helpbar__link">
            <i class="fas fa-phone" aria-hidden="true"></i> 037-2237169
        </a>
        <a href="tel:1919" class="nwpc-chat-helpbar__link">
            <i class="fas fa-headset" aria-hidden="true"></i> 1919
        </a>
    </div>

    <footer class="nwpc-chat-composer">
        <div class="nwpc-chat-composer__row">
            <textarea
                id="nwpcChatInput"
                class="nwpc-chat-composer__input"
                rows="1"
                placeholder="<?php echo htmlspecialchars($t['chat_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                aria-label="<?php echo htmlspecialchars($t['chat_placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                maxlength="500"
            ></textarea>
            <button
                type="button"
                id="nwpcChatSend"
                class="nwpc-chat-composer__send"
                disabled
                aria-label="<?php echo htmlspecialchars($t['send_message'], ENT_QUOTES, 'UTF-8'); ?>"
            >
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
            </button>
        </div>
        <p class="nwpc-chat-composer__hint"><?php echo $t['bot_disclaimer']; ?></p>
    </footer>
</div>

<script>
<?php
$llmConfigPath = __DIR__ . '/chatbot-config.local.php';
$llmEnabled = false;
if (is_file($llmConfigPath)) {
    $llmCfg = include $llmConfigPath;
    if (is_array($llmCfg)) {
        $key = trim((string) ($llmCfg['OPENAI_API_KEY'] ?? ''));
        $llmEnabled = $key !== '' && strpos($key, 'sk-your-key') !== 0;
    }
}
?>
window.CHATBOT_CONFIG = {
    lang: <?php echo json_encode($lang); ?>,
    knowledgeBase: <?php echo $jsonKnowledge; ?>,
    departments: <?php echo isset($jsonDepartments) ? $jsonDepartments : '[]'; ?>,
    llmEnabled: <?php echo $llmEnabled ? 'true' : 'false'; ?>,
    i18n: <?php echo json_encode([
        'fallback' => [
            'si' => 'කණගාටුයි, මට ඒ ගැන තොරතුරු නැහැ. කරුණාකර වෙනත් විදියකින් අහන්න, ප්‍රධාන ලේකම් කාර්යාලය (037-2237169) හෝ රාජ්‍ය තොරතුරු කේන්ද්‍රය (1919) අමතන්න.',
            'en' => 'Sorry, I do not have information about that. Please try rephrasing your question, contact the Chief Secretary\'s Office at 037-2237169, or dial the Government Information Center at 1919.',
            'ta' => 'மன்னிக்கவும், அந்த தகவல் என்னிடம் இல்லை. வேறு விதமாக கேளுங்கள், தலைமைச் செயலாளர் அலுவலகம் (037-2237169) அல்லது அரசாங்க தகவல் மையம் (1919) ஐ தொடர்பு கொள்ளவும்.',
        ][$lang],
        'welcome' => $t['bot_msg'],
        'disclaimer' => $t['bot_disclaimer'],
        'quickTopics' => $t['quick_topics'],
        'chooseDepartment' => $t['choose_department'] ?? 'Choose a department',
        'suggestedQuestions' => $t['suggested_questions'] ?? 'Suggested questions',
        'changeDepartment' => $t['change_department'] ?? 'Change department',
        'back' => $t['back'] ?? 'Back',
        'moreQuestions' => $t['more_questions'] ?? 'More questions',
        'typing' => $t['typing'],
        'you' => $t['chat_you'],
        'assistant' => $t['bot_title'],
    ], JSON_UNESCAPED_UNICODE); ?>
};
</script>
<script src="chatbot-matching.js" defer></script>
<script src="chatbot.js" defer></script>
