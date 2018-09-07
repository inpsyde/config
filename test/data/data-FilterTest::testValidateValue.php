<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\FilterTest;

/**
 * @see FilterTest::testValidateValue()
 */
return [
    'php standard filter float' => [
        'value' => '3.5',
        'schema' => [
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        'expected' => true,
    ],
    'php standard filter bool true' => [
        'value' => 'true',
        'schema' => [
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        'expected' => true,
    ],
    'php standard filter bool false' => [
        'value' => 'false',
        'schema' => [
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        'expected' => true,
    ],
    'php standard filter integer' => [
        'value' => '0',
        'schema' => [
            'filter' => FILTER_VALIDATE_INT,
        ],
        'expected' => true,
    ],
    'custom filter callback' => [
        'value' => 'This string is <b>evil</b>',
        'schema' => [
            'filter' => function ($value) {
                return filter_var($value, FILTER_SANITIZE_STRING);
            },
        ],
        'expected' => true,
    ],
    'custom filter throws exception' => [
        'value' => 'This string is <b>evil</b>',
        'schema' => [
            'filter' => function ($value) {
                throw new InvalidArgumentException();
            },
        ],
        'expected' => false,
    ],
];
