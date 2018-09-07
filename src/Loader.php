<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\RuntimeException;

class Loader
{

    public static function loadFromArray(array $definition, array $config = []): Config
    {
        return (new ContainerFactory(new SchemaValidation()))
            ->buildContainer($definition, $config);
    }

    public static function loadFromFile(string $file, array $config = []): Config
    {
        if (! is_file($file) || ! is_readable($file)) {
            throw new RuntimeException("Config definition file is invalid: {$file}");
        }

        $require = function ($file) {
            return require $file;
        };
        $definition = $require($file);

        if (! is_array($definition)) {
            throw new RuntimeException("Config definition must be an array in {$file}");
        }

        return self::loadFromArray($definition, $config);
    }
}