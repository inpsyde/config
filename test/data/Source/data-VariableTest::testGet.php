<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\VariableTest;

/**
 * @see VariableTest::testGet()
 */
return [
    'existing value with default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => '5.5',
            'config' => [
                'some.config.key' => '5.5',
            ],
            'filter' => [
                'filterValue' => [
                    'expect' => 'once',
                    'return' => 5.5,
                ],
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
            ],
            'schema' => [
                'getKeys' => [
                    'return' => ['some.config.key'],
                ],
            ],
            'schemaReader' => [
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10.01,
                ],
            ],
        ],
        'expected' => 5.5,
    ],
    'existing value without default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => '5.5',
            'config' => [
                'some.config.key' => '5.5',
            ],
            'filter' => [
                'filterValue' => [
                    'expect' => 'once',
                    'return' => 5.5,
                ],
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
            ],
            'schema' => [
                'getKeys' => [
                    'return' => ['some.config.key'],
                ],
            ],
            'schemaReader' => [
                'hasDefault' => [
                    'return' => false,
                ],
                'defaultValue' => [
                    'return' => null,
                ],
            ],
        ],
        'expected' => 5.5,
    ],
    'not existing value returns default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => null,
            'config' => [],
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
            'schema' => [
                'getKeys' => [
                    'return' => ['some.config.key'],
                ],
            ],
            'schemaReader' => [
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10.1,
                ],
            ],
        ],
        'expected' => 10.1,
    ],
];
