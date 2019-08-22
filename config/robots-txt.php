<?php
return [
    'settings' => [
        'sitemaps' => [
            'use_app_host' => true
        ]
    ],
    'paths' => [
        'production' => [
            '*' => [
                '', // production env always allows all
            ]
        ]
    ],
    'sitemaps' => [
        'production' => [
            
        ]
    ]
];
