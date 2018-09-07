<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\FilterTest;

/**
 * @see FilterTest::testFilterValue()
 */
return [
    'php standard filter float' => [
        'value' => '3.5',
        'schema' => [
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        'expected' => 3.5,
    ],
    'php standard filter bool' => [
        'value' => 'false',
        'schema' => [
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        'expected' => false,
    ],
    'custom filter callback' => [
        'value' => 'This string is <b>evil</b>',
        'schema' => [
            'filter' => function ($value) {
                return filter_var($value, FILTER_SANITIZE_STRING);
            },
        ],
        'expected' => 'This string is evil',
    ],
];
