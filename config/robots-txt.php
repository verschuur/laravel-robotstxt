<?php
return [
    'settings' => [],
    'environments' => [
        'production' => [
            'paths' => [
                '*' => [
                    'disallow' => [
                        ''
                    ],
                    'allow' => []
                ],
            ],
            'sitemaps' => [
                'sitemap.xml'
            ]
        ]
    ]
];