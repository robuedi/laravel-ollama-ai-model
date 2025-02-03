<?php

return [

    'text_client_path' => env('AI_TEXT_CLIENT_PATH', 'http://ollama:11434'),

    'ollama_allowed_models' => env('OLLAMA_ALLOWED_MODELS', 'mistral,distilgpt-2')
];
