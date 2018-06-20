<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
use Inpsyde\Config\Schema;

final class WpOption implements Source
{

    /**
     * @var callable
     */
    private $optionLoader;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var SchemaReader
     */
    private $reader;

    /**
     * @var Filter
     */
    private $filter;

    public function __construct(
        callable $optionLoader,
        Schema $schema,
        Filter $filter = null,
        SchemaReader $reader = null
    ) {
        $this->optionLoader = $optionLoader;
        $this->schema = $schema;
        $this->filter = $filter
            ?: new Filter();
        $this->reader = $reader
            ?: new SchemaReader();
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new MissingConfig("Missing wp option config: '{$key}'");
        }

        $name = $this->reader->sourceName($key, $this->schema);
        $value = ($this->optionLoader)($name);

        if (false === $value && $this->reader->hasDefault($key, $this->schema)) {
            return $this->reader->defaultValue($key, $this->schema);
        }

        return $this->filter->filterValue(
            $value,
            $this->schema->getDefinition($key)
        );
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        $name = $this->reader->sourceName($key, $this->schema);
        if (! $name) {
            return false;
        }

        $defaultValue = $this->reader->hasDefault($key, $this->schema)
            ? $this->reader->defaultValue($key, $this->schema)
            : false;

        $value = ($this->optionLoader)($name, $defaultValue);

        if ($value === $defaultValue) {
            return true;
        }

        return false !== $value
            ? $this->filter->validateValue($value, $this->schema->getDefinition($key))
            : false;
    }

    public static function asWpOption(Schema $schema, Filter $filter = null): self
    {
        $filter or $filter = new Filter();

        return new self(
            function ($optionName, $defaultValue = false) {
                return get_option($optionName, $defaultValue);
            },
            $schema,
            $filter
        );
    }

    public static function asWpSiteoption(Schema $schema, Filter $filter = null): self
    {
        $filter or $filter = new Filter();

        return new self(
            function ($optionName, $defaultValue = false) {
                return get_site_option($optionName, $defaultValue);
            },
            $schema,
            $filter
        );
    }
}
