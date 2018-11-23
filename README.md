# Inpsyde Config

This library is an implementation of the [`inpsyde/config-interface`](https://github.com/inpsyde/config-interface/). It provides uniform configuration management and filtering no matter if the values are provided by environment variables, WordPress database, PHP constants or variables.

## Installation

```
$ composer require inpsyde/config
```

## Usage

### Build the Config object

Create a configuration object from a definition file:

    <?php
    namespace Your\Plugin;
    
    use Inpsyde\Config\Loader;
    
    /* @var \Inpsyde\Config\Config $config */
    $config = Loader::loadFromFile(__DIR__.'/config/plugin-config.php');

Build from an array:

    <?php
    namespace Your\Plugin;
    
    use Inpsyde\Config\Loader;
    use Inpsyde\Config\Config;
    
    /* @var Config $config */
    $config = Loader::loadFromArray( [ /* config definition */ ] );


### Configuration schema

The configuration definition follows an associative schema:

    string:configKey => array (
        'source' => string
        'source_name' => string
        'default_value' => mixed, optional
        'filter' => int|callable, optional
    )

Example:

    <?php
    // config/plugin-config.php
    
    namespace MyPlugin;
    
    use Inpsyde\Config\Source\Source;
    
    return [
        'message.api.endpoint' => [
            // The configuration is read from an environment variable
            'source' => Source::SOURCE_ENV,
            // This is the name of the env variable
            'source_name' => 'SOME_ENV_VARIABLE',
            // Optional: you can provide a default value as fallback if the variable is not set
            'default_value' => 'http://api.tld/endpoint',
            // Optional: If the variable is set, pass it to filter_var() using the following filter argument
            'filter' => FILTER_VALIDATE_URL,
        ],
        'domain.some.key' => [
            // In this case the option is read from WP site options
            'source' => Source::SOURCE_WP_SITEOPTION,
            // With this option key
            'source_name' => '_option_key',
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        /**
         * You can also provide callables as filter to do
         * more complex filtering
         */
        'domain.some.komplex_value' => [
            'source' => Source::SOURCE_WP_OPTION,
            'source_name' => '_option_key',
            'default_value' => null,
            'filter' => function($value): string {
    
                return filter_var(
                    $value,
                    FILTER_SANITIZE_ENCODED,
                    FILTER_FLAG_STRIP_HIGH & FILTER_FLAG_STRIP_BACKTICK
                );
            },
        ],
    ];


With this declaration in place reading configuration value is done like this:

    <?php
    
    $apiUrl = $config->get('message.api.endpoint');
    $floatValue = $config->get('domain.some.key');
    $customFilteredValue = $config->get('domain.some.komplex_value');


### Available sources

    <?php
    use Inpsyde\Config\Source\Source;
    
    Source::SOURCE_ENV
    Source::SOURCE_WP_OPTION
    Source::WP_SITE_OPTION
    Source::CONSTANT
    Source::VARIABLE

### Defining configuration values on runtime

Sometimes it's useful to define configuration values on runtime (`Source::VARIABLE`). This is how you can do:

    <?php
    // plugin-config.php
    
    namespace MyPlugin;
    
    use Inpsyde\Config\Source\Source;
    
    return [
        'myPlugin.baseDir' => [
            'source' => Source::VARIABLE,
        ],
    ];


    <?php
    // my-plugin.php
    
    namespace MyPlugin;
    
    use Inpsyde\Config\Loader;
    
    $container = Loader::loadFromFile(
        'plugin-config.php',
        [
            'myPlugin.baseDir' => __DIR__
        ]
    );

## Roadmap

 * Change current working name `inpsyde/dev1-config` to `inpsyde/config` if everyone agrees
 * Maybe think about namespace support of the keys to split config objects into sub-config that is only aware of a specific namespace. As namespace separator the `.` is considered to be used.
 * Maybe allow `callable` as default value factory
 * Define a stack of sources for a single key to fall back to another source if the primary one is not defined (e.g. allow a default setting for multisite that can be overridden for a single site)

## Crafted by Inpsyde

The team at [Inpsyde](https://inpsyde.com) is engineering the Web since 2006.

## License

Copyright (c) 2018 David Naber, Inpsyde GmbH

Good news, this plugin is free for everyone! Since it's released under the [MIT License](LICENSE) you can use it free of charge on your personal or commercial website.

## Contributing

All feedback, bug reports and pull requests are welcome.
