<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Exception\InvalidSchema;
use Inpsyde\Config\Source\Source;

$filterCallback01 = function ($value) {
    return filter_var($value, FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_BACKTICK & FILTER_FLAG_STRIP_LOW);
};

return [
    'testValidateSchema' => [
        '01: test complete valid definition' => [
            'schema' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VARIABLE',
                    'default_value' => '',
                    'filter' => $filterCallback01,
                ],
            ],
            'expected' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VARIABLE',
                    'default_value' => '',
                    'filter' => $filterCallback01,
                ],
            ],
        ],
        '02: no filter given' => [
            'schema' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VARIABLE',
                    'default_value' => '',
                ],
            ],
            'expected' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VARIABLE',
                    'default_value' => '',
                    'filter' => null,
                ],
            ],
        ],
        '03: no default value given' => [
            'schema' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VARIABLE',
                    'filter' => FILTER_SANITIZE_EMAIL,
                ],
            ],
            'expected' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                    'source_name' => 'SOME_ENV_VARIABLE',
                    'filter' => FILTER_SANITIZE_EMAIL,
                ],
            ],
        ],
        '04: source variable does not require a source_name' => [
            'schema' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_VARIABLE,
                    'filter' => FILTER_SANITIZE_EMAIL,
                ],
            ],
            'expected' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_VARIABLE,
                    'filter' => FILTER_SANITIZE_EMAIL,
                    'source_name' => null,
                ],
            ],
        ],
        '05: source variable overrides source_name' => [
            'schema' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_VARIABLE,
                    'source_name' => 'whatever',
                    'filter' => FILTER_SANITIZE_EMAIL,
                ],
            ],
            'expected' => [
                'some.config.key' => [
                    'source' => \Inpsyde\Config\Source\Source::SOURCE_VARIABLE,
                    'source_name' => null,
                    'filter' => FILTER_SANITIZE_EMAIL,
                ],
            ],
        ],
    ],
    'testValidateSchemaThrowsException' => [
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
    ],
];
