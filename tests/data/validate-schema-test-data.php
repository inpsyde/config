<?php
declare(strict_types = 1);

namespace Inpsyde\Config\Test\Data;

$test02Closure = function (): string {

    return 'default value';
};

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
                    'filter_cb' => function () {
                    },
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
                    'filter_cb' => function () {
                    },
                    'filter_options' => [],
                ],
            ],
        ]
    ],
];
