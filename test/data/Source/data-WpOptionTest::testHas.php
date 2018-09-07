<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\WpOptionTest;

/**
 * @see WpOptionTest::testHas()
 */
return [
    'existing value with default' => [
        'key' => 'some.config.key',
        'definitionForKey' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => '10',
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
                    'return' => '_option_name',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 11,
                ],
            ],
        ],
        'expected' => true,
    ],
    'existing value without default' => [
        'key' => 'some.config.key',
        'definitionForKey' => [
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => '10',
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
                    'return' => '_option_name',
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
        'definitionForKey' => [
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
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
                    'return' => '_option_name',
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
        'definitionForKey' => [
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
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
                    'return' => '_option_name',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10,
                ],
            ],
        ],
        'expected' => false,
    ],
    'not existing value falls back to default value' => [
        'key' => 'some.config.key',
        'definitionForKey' => [
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
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
                    'return' => '_option_name',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 10,
                ],
            ],
        ],
        'expected' => true,
    ],
    'not existing value' => [
        'key' => 'some.config.key',
        'definitionForKey' => [
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => null,
            'filter' => [
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'validateValue' => [
                    'expect' => 'never',
                    'return' => true,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => '_option_name',
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
