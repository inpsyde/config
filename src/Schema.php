<?php
declare(strict_types=1);

namespace Inpsyde\Config;

class Schema
{

    private $definitions = [];

    private $definitionsBySource = [];

    /**
     * No validation happens here. Use SchemaValidation instead
     */
    public function __construct(array $schema)
    {
        array_walk(
            $schema,
            [
                $this,
                'addDefinition',
            ]
        );
    }

    /**
     * @return string[]
     */
    public function getKeys(string $source = ''): array
    {
        return $source
            ? array_keys($this->getKeysForSource($source))
            : array_keys($this->definitions);
    }

    public function getDefinition(string $key): array
    {
        if (! array_key_exists($key, $this->definitions)) {
            return [];
        }

        return $this->definitions[$key];
    }

    private function getKeysForSource(string $source): array
    {
        if (! array_key_exists($source, $this->definitionsBySource)) {
            return [];
        }

        return $this->definitionsBySource[$source];
    }

    private function addDefinition($definition, $key)
    {
        $source = $definition['source'];
        $this->definitions[$key] = $definition;
        if (! array_key_exists($source, $this->definitionsBySource)) {
            $this->definitionsBySource[$source] = [];
        }
        $this->definitionsBySource[$source][$key][] = $definition;
    }
}
