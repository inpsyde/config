<?php
declare(strict_types=1);

return [
    'inpsyde.config.loaderTest' => [
        'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
        'source_name' => 'CONFIG_LOADER_TEST',
        'filter' => FILTER_VALIDATE_FLOAT,
    ]
];
