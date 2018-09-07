<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\VariableTest;

/**
 * @see VariableTest::testHas()
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
                    'expect' => 'never',
                    'return' => null,
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
                    'return' => 10.1,
                ],
            ],
        ],
        'expected' => true,
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
                    'expect' => 'never',
                    'return' => null,
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
        'expected' => true,
    ],
    'existing invalid value without default' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => 'foo',
            'config' => [
                'some.config.key' => 'foo',
            ],
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
        'expected' => false,
    ],
    'invalid value does not fall back to default value' => [
        'key' => 'some.config.key',
        'mockData' => [
            'rawValue' => 'foo',
            'config' => [
                'some.config.key' => 'foo',
            ],
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
        'expected' => false,
    ],
    'not existing value fall back to default value' => [
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
                    'return' => [],
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
        'expected' => true,
    ],
    'not existing value without default' => [
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
                    'return' => [],
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
        'expected' => false,
    ],
];
