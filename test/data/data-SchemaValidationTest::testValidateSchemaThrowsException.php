<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Exception\InvalidSchema;
use Inpsyde\Config\Source\Source;

return [
    'missing config key' => [
        'schema' => [
            [
                'source' => Source::SOURCE_ENV,
                'name' => 'WHATEVER',
            ],
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'duplicate key' => [
        'schema' => [
            'some.config.key' => [],
            'some.config.key' => [],
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'definition not array' => [
        'schema' => [
            'some.config.key' => 'foo',
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'empty definition' => [
        'schema' => [
            'some.config.key' => [],
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'definition missing source' => [
        'schema' => [
            'some.config.key' => [
                'source_name' => 'foo',
            ],
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'definition missing name' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
            ],
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'filter invalid' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
                'source_name' => '_my_siteoption',
                'filter' => 'FILTER_VALIDATE_BOOLEAN',
            ],
        ],
        'expectedException' => InvalidSchema::class,
    ],
    'filter unknown' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
                'source_name' => '_my_siteoption',
                'filter' => PHP_INT_MAX,
            ],
        ],
        'expectedException' => InvalidSchema::class,
    ],
];
