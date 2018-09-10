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
                'sourceName' => 'SOME_ENV_VARIABLE',
                'defaultValue' => '',
                'filter' => $filterCallback,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'SOME_ENV_VARIABLE',
                'defaultValue' => '',
                'filter' => $filterCallback,
            ],
        ],
    ],
    '02: no filter given' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'SOME_ENV_VARIABLE',
                'defaultValue' => '',
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'SOME_ENV_VARIABLE',
                'defaultValue' => '',
                'filter' => null,
            ],
        ],
    ],
    '03: no default value given' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'SOME_ENV_VARIABLE',
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_ENV,
                'sourceName' => 'SOME_ENV_VARIABLE',
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
    ],
    '04: source variable does not require a sourceName' => [
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
                'sourceName' => null,
            ],
        ],
    ],
    '05: source variable overrides sourceName' => [
        'schema' => [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
                'sourceName' => 'whatever',
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
                'sourceName' => null,
                'filter' => FILTER_SANITIZE_EMAIL,
            ],
        ],
    ],
];
