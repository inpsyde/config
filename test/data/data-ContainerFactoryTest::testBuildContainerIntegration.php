<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\Source;

return [
    'single environment source' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'KEY_1',
                'filter' => FILTER_VALIDATE_FLOAT,
            ],
        ],
        'config' => [
        ],
        'mockData' => [
            'env' => [
                'KEY_1' => '3.1415',
            ],
        ],
        'expected' => [
            'key.1' => 3.1415,
        ],
    ],
    'single variable source' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_VARIABLE,
            ],
        ],
        'config' => [
            'key.1' => 'http://localhost:9200',
        ],
        'mockData' => [
        ],
        'expected' => [
            'key.1' => 'http://localhost:9200',
        ],
    ],
    'single wp_option source' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_WP_OPTION,
                'sourceName' => 'home',
            ],
        ],
        'config' => [],
        'mockData' => [
            'wp_option' => [
                [
                    'key' => 'home',
                    'default' => false,
                    'return' => 'http://wp.localhost',
                ],
            ],
        ],
        'expected' => [
            'key.1' => 'http://wp.localhost',
        ],
    ],
    'single wp_siteoption source' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
                'sourceName' => 'super_admins',
            ],
        ],
        'config' => [],
        'mockData' => [
            'wp_siteoption' => [
                [
                    'key' => 'super_admins',
                    'default' => false,
                    'return' => [ 1, 2, 3 ],
                ],
            ],
        ],
        'expected' => [
            'key.1' => [1, 2, 3],
        ],
    ],
    'multiple env with default value' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'API_URL',
                'defaultValue' => 'http://localhost:9200'
            ],
            'key.2' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'API_KEY',
            ]
        ],
        'config' => [],
        'mockData' => [
            'env' => [
                'API_KEY' => 'abc123'
            ],
        ],
        'expected' => [
            'key.1' => 'http://localhost:9200',
            'key.2' => 'abc123'
        ],
    ],
    'applied custom filter' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'API_URL',
                'filter' => function($value) {
                    return $value . '?user=me';
                }
            ],
        ],
        'config' => [],
        'mockData' => [
            'env' => [
                'API_URL' => 'http://localhost:9300'
            ],
        ],
        'expected' => [
            'key.1' => 'http://localhost:9300?user=me',
        ],
    ],
    'combined sources' => [
        'definition' => [
            'key.1' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'API_URL',
                'filter' => function($value) {
                    return $value . '?user=me';
                }
            ],
            'key.2' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'API_KEY',
                'defaultValue' => 'abc123',
            ],
            'key.3' => [
                'source' => Source::SOURCE_VARIABLE,
            ],
            'key.4' => [
                'source' => Source::SOURCE_WP_OPTION,
                'sourceName' => 'home',
            ],
            'key.5' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
                'sourceName' => 'super_admins',
            ],
        ],
        'config' => [
            'key.3' => 'lorem ipsum',
        ],
        'mockData' => [
            'env' => [
                'API_URL' => 'http://localhost:9300'
            ],
            'wp_option' => [
                [
                    'key' => 'home',
                    'default' => false,
                    'return' => 'http://wp.localhost',
                ]
            ],
            'wp_siteoption' => [
                [
                    'key' => 'super_admins',
                    'default' => false,
                    'return' => [1,2],
                ]
            ]
        ],
        'expected' => [
            'key.1' => 'http://localhost:9300?user=me',
            'key.2' => 'abc123',
            'key.3' => 'lorem ipsum',
            'key.4' => 'http://wp.localhost',
            'key.5' => [1,2]
        ],
    ],
];
