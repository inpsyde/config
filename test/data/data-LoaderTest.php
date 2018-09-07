<?php
declare(strict_types=1);

namespace Inpsyde\Config\Test\Data;

use Inpsyde\Config\LoaderTest;
use Inpsyde\Config\Source\Source;

/**
 * @see LoaderTest
 */
return [
    'inpsyde.config.loaderTest' => [
        'source' => Source::SOURCE_ENV,
        'source_name' => 'CONFIG_LOADER_TEST',
        'filter' => FILTER_VALIDATE_FLOAT,
    ]
];
