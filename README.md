# Inpsyde Config

Key-value config management. The package provides a simple interface to read configuration regardless how the configuration is actually provided (either by an environment variable or WordPress option table or a PHP constant).

The package provides declaration schema for configuration sources and filtering/validating.

## Installation

```
$ composer require inpsyde/config
```

## Why

When it comes to more complex plugins you want to have a reliable and uniform way to access your configuration. Instead of coupling your business logic to details about configuration you can depend on an abstract configuration interface.

## Usage

### Build the Config object

Build from a definition file:

    <?php
    namespace Your\Plugin;
    
    use Inpsyde\Config\Loader;
    
    Loader::loadFromFile(__DIR__.'/config/config-definition.php');

Build from an array:

    <?php
    namespace Your\Plugin;
    
    use Inpsyde\Config\Loader;
    use Inpsyde\Config\Config;
    
    /* @var Config $config */
    $config = Loader::loadFromArray( [ /* config definition */ ] );

### The Config interface

```
namespace Inpsyde\Config;

interface Config
{

    /**
     * @return mixed
     */
    public function get(string $key);

    public function has(string $key) : bool;
}
```
This interface reminds of PSR-11 and I considered to extend or simply use PRS-11 as interface but the documentation says that it is explicitly meant as common interface for [_dependency injection containers_](https://www.php-fig.org/psr/psr-11/).

Also mixing up DI-Containers with config containers is not a good thing as both targeting different purposes.


### Configuration schema

The configuration definition follows an associative schema:

    configKey => definition

Example:

    return [
        'message.api.endpoint' => [
            // The configuration is read from an environment variable
            'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
            // This is the name of this env variable
            'source_name' => 'SOME_ENV_VARIABLE',
            // Optional: you can provide a default value as fallback if the variable is not set
            'default_value' => 'http://api.tld/endpoint',
            // Optional: If the variable is set, pass it throu filter_var() with the following filter
            'filter' => FILTER_VALIDATE_URL,
        ],
        'domain.some.key' => [
            // In this case the option is read from WP site options
            'source' => \Inpsyde\Config\Source\Source::SOURCE_WP_SITEOPTION,
            // With this option key
            'source_name' => '_option_key',
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
        /**
         * You can also provide callables as filter to do
         * more complex filtering
         */
        'domain.some.komplex_value' => [
            'source' => \Inpsyde\Config\Source\Source::SOURCE_WP_OPTION,
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


With this declaration in place getting the configuration is as easy as:

```
$apiUrl = $config->get('message.api.endpoint');
$floatValue = $config->get('domain.some.key');
$customFilteredValue = $config->get('domain.some.komplex_value');
```

### Available sources

    use Inpsyde\Config\Source
    
    Source::SOURCE_ENV
    Source::SOURCE_WP_OPTION
    Source::WP_SITE_OPTION
    Source::CONSTANT
    Source::VARIABLE

## Roadmap

 * Complete tests and implementation
 * Change current working name `inpsyde/dev1-config` to `inpsyde/config` if everyone agrees
 * Maybe think about namespace support to split config objects into sub-config that is only aware of a specific namespace. As namespace separator the `.` is considered to be used.
 * Maybe allow `callable` as default value factory
 * Define a stack of sources for a single key to fall back to another source if the primary one is not defined (e.g. allow a default setting for multisite that can be overridden for a single site)

## Crafted by Inpsyde

The team at [Inpsyde](https://inpsyde.com) is engineering the Web since 2006.

## License

Copyright (c) 2018 David Naber, Inpsyde

Good news, this plugin is free for everyone! Since it's released under the [MIT License](LICENSE) you can use it free of charge on your personal or commercial website.

## Contributing

All feedback / bug reports / pull requests are welcome.
