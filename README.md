# Inpsyde Config

Key-value config management. The package provides a simple interface to read configuration regardless where the configuration is actually stored (either in an environment variable or WordPress option table or constant).

The implementation provides an easy-to-use declaration for configuration sources and filtering/validating.

## Installation

```
$ composer require inpsyde/config
```

## Usage

### Config interface

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

Also mixing up DI-Containers with config containers is not a good thing as both 


### Setup/Config declaration

```php
return [
    'message.api.endpoint' => [
        'source' => \Inpsyde\Config\Source\Source::SOURCE_ENV,
        'source_name' => 'SOME_ENV_VARIABLE',
        'default_value' => 'http://api.tld/endpoint',
        'filter' => [
            'filter' => FILTER_VALIDATE_URL,
        ],
    ],
    'domain.some.key' => [
        'source' => \Inpsyde\Config\Source\Source::SOURCE_WP_SITEOPTION,
        'source_name' => '_option_key',
        'default_value' => 2.5,
        'filter' => [
            'filter' => FILTER_VALIDATE_FLOAT,
        ],
    ],
    /**
     * even though the following example doesn't make
     * much sense, it is just in place to demonstrate
     * the filter callback method
     */
    'domain.some.komplex_value' => [
        'source' => \Inpsyde\Config\Source\Source::SOURCE_WP_OPTION,
        'source_name' => '_option_key',
        'default_value' => null,
        'filter' => [
            'filter' => FILTER_CALLBACK,
            'filter_cb' => function($userId): \WP_User {
                $user = wp_get_user($userId);
                if (!$user instance of \WP_User) {
                    throw new ConfigException();
                }
                
                return $user;
            }
        ],
    ],
];
```

With this declaration in place getting the configuration is as easy as:

```
$apiUrl = $config->get('message.api.endpoint');
$floatValue = $config->get('domain.some.key');
$superUser = $config->get('domain.some.komplex_value');
```

## Roadmap

 * Complete tests and implementation
 * Change current working name `inpsyde/dev1-config` to `inpsyde/config` if everyone agrees
 * Maybe think about namespace support to split config objects into sub-config that is only aware of a specific namespace. As namespace separator the `.` is considered to be used.

## Crafted by Inpsyde

The team at [Inpsyde](https://inpsyde.com) is engineering the Web since 2006.

## License

Copyright (c) 2018 David Naber, Inpsyde

Good news, this plugin is free for everyone! Since it's released under the [MIT License](LICENSE) you can use it free of charge on your personal or commercial website.

## Contributing

All feedback / bug reports / pull requests are welcome.
