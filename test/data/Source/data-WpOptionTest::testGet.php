<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\WpOptionTest;

/**
 * @see WpOptionTest::testGet()
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
                    'expect' => 'once',
                    'return' => 10,
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
                    'return' => 9,
                ],
            ],
        ],
        'expected' => 10,
    ],
    'existing value without default' => [
        'key' => 'some.config.key',
        'definitionForKey' => [
            'Im Just Arbitrary Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => '10.01',
            'filter' => [
                'filterValue' => [
                    'expect' => 'once',
                    'return' => 10.01,
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
        'expected' => 10.01,
    ],
    'not existing value returns default' => [
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
                    'return' => 9.0,
                ],
            ],
        ],
        'expected' => 9.0,
    ],
];
