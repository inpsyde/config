<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\ConstantTest;

/**
 * @see ConstantTest::testGet()
 */
return [
    'existing value with default' => [
        'key' => 'some.constant.config',
        'definitionForKey' => [
            /*
            * It doesn't matter how this array looks like as it is only passed through
            *  and is expected as parameter for mocks
            */
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => '10',
            'filter' => [
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
                'filterValue' => [
                    'expect' => 'once',
                    'return' => 10,
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
                    'return' => 5,
                ],
            ],
        ],
        'expected' => 10,
    ],
    'existing value without default' => [
        'key' => 'some.constant.config',
        'definition' => [
            'Im Just Random Data That Gets Passed Around',
        ],
        'mockData' => [
            'rawValue' => 'http://some.url',
            'filter' => [
                'validateValue' => [
                    'expect' => 'once',
                    'return' => true,
                ],
                'filterValue' => [
                    'expect' => 'once',
                    'return' => 'http://some.url',
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
        'expected' => "http://some.url",
    ],
    'not existing value with default' => [
        'key' => 'some.constant.config',
        'definition' => [
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
                    'return' => 10.01,
                ],
            ],
        ],
        'expected' => 10.01,
    ],
];

