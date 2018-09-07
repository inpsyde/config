<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\SchemaValidationTest;
use \Inpsyde\Config\Source\Source;

$filterCallback = function ($value) {
    return filter_var(
        $value,
        FILTER_SANITIZE_ENCODED,
        FILTER_FLAG_STRIP_BACKTICK & FILTER_FLAG_STRIP_LOW
    );
};

/**
 * @see SchemaValidationTest::testValidateSchema()
 */
return [
    '01: test complete valid definition' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'default_value' => '',
                'filter' => $filterCallback,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'default_value' => '',
                'filter' => $filterCallback,
            ],
        ],
    ],
    '02: no filter given' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'default_value' => '',
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'default_value' => '',
                'filter' => null,
            ],
        ],
    ],
    '03: no default value given' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
    ],
    '04: source variable does not require a source_name' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
                'filter' => FILTER_SANITIZE_EMAIL,
                'source_name' => null,
            ],
        ],
    ],
    '05: source variable overrides source_name' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
                'source_name' => 'whatever',
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
                'source_name' => null,
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
    ],
];
