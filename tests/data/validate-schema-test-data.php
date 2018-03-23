<?php
declare(strict_types = 1);

namespace Inpsyde\Config\Test\Data;

$test02Closure = function (): string {

    return 'default value';
};

$filterCallback01 = function(){};

return [
    '01: test complete valid definition' => [
        'schema' => [
            'some.config.key' => [
                'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'default_value' => '',
                'filter' => [
                    'filter' => FILTER_CALLBACK,
                    'filter_flags' => null,
                    'filter_cb' => $filterCallback01,
                    'filter_options' => [],
                ],
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'default_value' => '',
                'filter' => [
                    'filter' => FILTER_CALLBACK,
                    'filter_flags' => null,
                    'filter_cb' => $filterCallback01,
                    'filter_options' => [],
                ],
            ],
        ]
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
        ]
    ],
    '03: no default value given' => [
        'schema' => [
            'some.config.key' => [
                'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'filter' => [
                    'filter' => FILTER_SANITIZE_EMAIL,
                ],
            ],
        ],
        'expected' => [
            'some.config.key' => [
                'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
                'source_name' => 'SOME_ENV_VARIABLE',
                'filter' => [
                    'filter' => FILTER_SANITIZE_EMAIL,
                    'filter_options' => [],
                    'filter_flags' => null,
                    'filter_cb' => null,
                ],
            ],
        ]
    ],
];
