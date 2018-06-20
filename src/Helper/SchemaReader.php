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
            : $definition['source_name'];
    }

    public function hasDefault(string $key, Schema $schema): bool
    {

        return array_key_exists(
            'default_value',
            $schema->getDefinition($key)
        );
    }

    public function defaultValue(string $key, Schema $schema)
    {
        if (! $this->hasDefault($key,$schema)) {
            throw new MissingDefaultValue("Key: '{$key}'");
        }

        return $schema->getDefinition($key)['default_value'];
    }
}