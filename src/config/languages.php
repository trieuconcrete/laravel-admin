<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | List of language codes and their display names
    |
    */
    
    'supported' => [
        'vi' => [
            'name' => 'Tiếng Việt',
            'flag' => '🇻🇳',
            'native' => 'Tiếng Việt',
            'rtl' => false
        ],
        'en' => [
            'name' => 'English',
            'flag' => '🇬🇧',
            'native' => 'English',
            'rtl' => false
        ],
        'ja' => [
            'name' => 'Japanese',
            'flag' => '🇯🇵',
            'native' => '日本語',
            'rtl' => false
        ]
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    */
    
    'default' => env('APP_LOCALE', 'vi'),
    
    /*
    |--------------------------------------------------------------------------
    | Hide Default Language in URL
    |--------------------------------------------------------------------------
    |
    | When true, the default language will not be shown in the URL
    | Example: site.com/about (for default language) vs site.com/en/about
    |
    */
    
    'hide_default' => true,
];