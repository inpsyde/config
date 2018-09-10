<?php
declare(strict_types=1);

namespace Inpsyde\Config\Helper;

use Inpsyde\Config\Exception\MissingDefaultValue;
use Inpsyde\Config\Schema;

class SchemaReader
{

    public function sourceName(string $key, Schema $schema): string
    {
        $definition = $schema->getDefinition($key);

        return empty($definition)
            ? ''
            : $definition['sourceName'];
    }

    public function hasDefault(string $key, Schema $schema): bool
    {

        return array_key_exists(
            'defaultValue',
            $schema->getDefinition($key)
        );
    }

    public function defaultValue(string $key, Schema $schema)
    {
        if (! $this->hasDefault($key,$schema)) {
            throw new MissingDefaultValue("Key: '{$key}'");
        }

        return $schema->getDefinition($key)['defaultValue'];
    }
}
