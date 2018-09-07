<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\EnvironmentTest;

/**
 * @see EnvironmentTest::testHas()
 */
return [
    'existing value with default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => '5.5',
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'INPSYDE_CONFIG_TEST_A',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10.1,
                ],
            ],
        ],
        'expected' => true,
    ],
    'existing value without default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => "10.01",
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'INPSYDE_CONFIG_TEST_B',
                ],
                'hasDefault' => [
                    'return' => false,
                ],
                'defaultValue' => [
                    'return' => null,
                ],
            ],
        ],
        'expected' => true,
    ],
    'existing invalid value without default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => 'foo',
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'once',
                    'return' => false,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'INPSYDE_CONFIG_TEST_B',
                ],
                'hasDefault' => [
                    'return' => false,
                ],
                'defaultValue' => [
                    'return' => null,
                ],
            ],
        ],
        'expected' => false,
    ],
    'invalid value does not fall back to default value' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => 'foo',
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'once',
                    'return' => false,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'INPSYDE_CONFIG_TEST_B',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10.01,
                ],
            ],
        ],
        'expected' => false,
    ],
    'not existing value fall back to default value' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => null,
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'INPSYDE_CONFIG_TEST_C',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10.1,
                ],
            ],
        ],
        'expected' => true,
    ],
    'not existing value without default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => null,
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'INPSYDE_CONFIG_TEST_C',
                ],
                'hasDefault' => [
                    'return' => false,
                ],
                'defaultValue' => [
                    'return' => null,
                ],
            ],
        ],
        'expected' => false,
    ],
];
