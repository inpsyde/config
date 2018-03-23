<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

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
    ],
];
