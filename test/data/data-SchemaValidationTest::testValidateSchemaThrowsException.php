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
        'expectedExceptionMessage' => 'Schema must be an associative array',
    ],
    'definition not array' => [
        'schema' => [
            'some.config.key' => 'foo',
        ],
        'expectedException' => InvalidSchema::class,
        'expectedExceptionMessage' => 'Key definition must be an array',
    ],
    'empty definition' => [
        'schema' => [
            'some.config.key' => [],
        ],
        'expectedException' => InvalidSchema::class,
        'expectedExceptionMessage' => "Missing definition 'source' for key 'some.config.key'",
    ],
    'definition missing source' => [
        'schema' => [
            'some.config.key' => [
                'source_name' => 'foo',
            ],
        ],
        'expectedException' => InvalidSchema::class,
        'expectedExceptionMessage' => "Missing definition 'source' for key 'some.config.key'",
    ],
    'definition missing name' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
            ],
        ],
        'expectedException' => InvalidSchema::class,
        'expectedExceptionMessage' => "Missing definition 'source_name' for key 'some.config.key'",
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
        'expectedExceptionMessage' => "Filter must be either callable or integer for key 'some.config.key'",
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
        'expectedExceptionMessage' => "Invalid filter for key 'some.config.key'",
    ],
];
