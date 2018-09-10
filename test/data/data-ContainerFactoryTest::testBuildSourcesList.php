<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\Source\Constant;
use Inpsyde\Config\Source\Environment;
use Inpsyde\Config\Source\Source;
use Inpsyde\Config\Source\Variable;
use Inpsyde\Config\Source\WpOption;

return [
    [
        'definition' => [
            'config.env.one' => [
                'source' => Source::SOURCE_ENV,
            ],
            'config.env.two' => [
                'source' => Source::SOURCE_ENV,
            ],
            'config.env.three' => [
                'source' => Source::SOURCE_ENV,
            ],
            'config.option.one' => [
                'source' => Source::SOURCE_WP_OPTION,
            ],
            'config.option.two' => [
                'source' => Source::SOURCE_WP_OPTION,
            ],
            'config.siteoption.one' => [
                'source' => Source::SOURCE_WP_SITEOPTION,
            ],
            'config.constant.one' => [
                'source' => Source::SOURCE_CONSTANT,
            ],
            'config.constant.two' => [
                'source' => Source::SOURCE_CONSTANT,
            ],
            'config.variable.one' => [
                'source' => Source::SOURCE_VARIABLE,
            ],
        ],
        'variableConfig' => [
            'config.variable.one' => true,
        ],
        'keysBySource' => [
            Source::SOURCE_ENV => [
                'config.env.one',
                'config.env.two',
                'config.env.three',
            ],
            Source::SOURCE_WP_OPTION => [
                'config.option.one',
                'config.option.two',
            ],
            Source::SOURCE_WP_SITEOPTION => [
                'config.siteoption.one',
            ],
            Source::SOURCE_CONSTANT => [
                'config.constant.one',
                'config.constant.two',
            ],
            Source::SOURCE_VARIABLE => [
                'config.variable.one',
            ],
        ],
        'expectations' => [
            'config.env.one' => Environment::class,
            'config.env.two' => Environment::class,
            'config.env.three' => Environment::class,
            'config.option.one' => WpOption::class,
            'config.option.two' => WpOption::class,
            'config.siteoption.one' => WpOption::class,
            'config.constant.one' => Constant::class,
            'config.constant.two' => Constant::class,
            'config.variable.one' => Variable::class,
        ],
    ],
];
