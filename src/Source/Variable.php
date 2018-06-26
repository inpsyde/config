<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\UnknownKey;
use Inpsyde\Config\Exception\MissingValue;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
use Inpsyde\Config\Schema;

final class Variable implements Source
{

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var SchemaReader
     */
    private $reader;

    /**
     * @var array
     */
    private $config;

    /**
     * @throws MissingValue
     */
    public function __construct(Schema $schema, array $config, Filter $filter = null, SchemaReader $reader = null)
    {
        $this->schema = $schema;
        $this->filter = $filter
            ?: new Filter();
        $this->reader = $reader
            ?: new SchemaReader();
        $this->validateConfig($config);
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new UnknownKey("Key: {$key}");
        }
        if (array_key_exists($key, $this->config)) {
            $definition = $this->schema->getDefinition($key);

            return $this->filter->filterValue($this->config[$key], $definition);
        }

        return $this->reader->defaultValue($key, $this->schema);
    }

    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->config)) {
            $definition = $this->schema->getDefinition($key);

            return $this->filter->validateValue($this->config[$key], $definition);
        }

        return $this->reader->hasDefault($key, $this->schema);
    }

    /**
     * @throws MissingValue
     */
    private function validateConfig(array $config)
    {
        $diff = array_diff(
            $this->schema->getKeys(Source::SOURCE_VARIABLE),
            array_keys($config)
        );

        $diff = array_filter(
            $diff,
            function ($key) {
                return ! $this->reader->hasDefault($key, $this->schema);
            }
        );

        if (! empty($diff)) {
            throw new MissingValue("Keys: ". implode(',', $diff));
        }
    }
}