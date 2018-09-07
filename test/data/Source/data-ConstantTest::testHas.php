<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\ConstantTest;

/**
 * @see ConstantTest::testHas
 */
return [
    'existing value with default' => [
        'key' => 'some.constant.config',
        'definition' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => 'https://some.url',
            'filter' => [
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'CONSTANT_A',
                ],
                'hasDefault' => [
                    'return' => true,
                ],
                'defaultValue' => [
                    'return' => 'https://default.url',
                ],
            ],
        ],
        'expected' => true,
    ],
    'existing value without default' => [
        'key' => 'some.constant.config',
        'definition' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => 'https://some.url',
            'filter' => [
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'CONSTANT_A',
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
        'key' => 'some.constant.config',
        'definition' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => 'not-a-valid-number',
            'filter' => [
                'validateValue' => [
                    'expect' => 'once',
                    'return' => false,
                ],
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'CONSTANT_A',
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
    'invalid value does not fall back to default' => [
        'key' => 'some.constant.config',
        'definition' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => 'not-a-valid-number',
            'filter' => [
                'validateValue' => [
                    'expect' => 'once',
                    'return' => false,
                ],
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'CONSTANT_A',
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
    'not existing value falls back to default' => [
        'key' => 'some.constant.config',
        'definition' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => null,
            'filter' => [
                'validateValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'CONSTANT_A',
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
    'not existing value without default' => [
        'key' => 'some.constant.config',
        'definition' => [
            /*
             * It doesn't matter how this array looks like as it is only passed through
             *  and is expected as parameter for mocks
             */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => null,
            'filter' => [
                'validateValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
                'filterValue' => [
                    'expect' => 'never',
                    'return' => null,
                ],
            ],
            'schemaReader' => [
                'sourceName' => [
                    'return' => 'CONSTANT_A',
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
