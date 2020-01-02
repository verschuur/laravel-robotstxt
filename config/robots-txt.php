<?php
return [
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