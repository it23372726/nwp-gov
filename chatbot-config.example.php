<?php
/**
 * Example chatbot LLM config.
 * Copy to chatbot-config.local.php and set your key.
 *
 * OpenAI-compatible APIs work (OpenAI, Azure OpenAI proxies, Groq, etc.).
 */
return [
    // Required for LLM fallback
    'OPENAI_API_KEY' => 'sk-your-key-here',

    // Default OpenAI; change for compatible providers
    'OPENAI_BASE_URL' => 'https://api.openai.com/v1',

    // Lightweight, cheap model for intent classification
    'OPENAI_MODEL' => 'gpt-4o-mini',

    // Optional hard timeout (seconds)
    'OPENAI_TIMEOUT' => 12,
];
