<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Exception\MissingDefaultValue;
use Inpsyde\Config\Filter;
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
     * @var Filter
     */
    private $filter;

    public function __construct(callable $optionLoader, Schema $schema, Filter $filter = null)
    {
        $this->optionLoader = $optionLoader;
        $this->filter = $filter;
        $this->schema = $schema;
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new MissingConfig("Missing wp option config: '{$key}'");
        }

        $name = $this->getName($key);
        $value = ($this->optionLoader)($name);

        if (false === $value && $this->hasDefault($key)) {
            return $this->getDefault($key);
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
        $name = $this->getName($key);
        if (! $name) {
            return false;
        }

        $defaultValue = $this->hasDefault($key)
            ? $this->getDefault($key)
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

    private function getName(string $key): string
    {
        $definition = $this->schema->getDefinition($key);

        return empty($definition)
            ? ''
            : $definition['source_name'];
    }

    private function hasDefault(string $key): bool
    {
        return array_key_exists(
            'default_value',
            $this->schema->getDefinition($key)
        );
    }

    /**
     * @throws MissingDefaultValue
     */
    private function getDefault(string $key)
    {
        if (! $this->hasDefault($key)) {
            throw new MissingDefaultValue("Key: '{$key}'");
        }

        return $this->schema->getDefinition($key)['default_value'];
    }
}
