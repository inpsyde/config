<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Exception\MissingDefaultValue;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Schema;

final class Environment implements Source
{

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @param Filter $filter
     * @param Schema $schema
     */
    public function __construct(Schema $schema, Filter $filter = null)
    {
        $this->schema = $schema;
        $this->filter = $filter
            ?: new Filter();
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new MissingConfig("Missing env config: '{$key}'");
        }

        $name = $this->getName($key);
        $value = getenv($name);
        if (false === $value && $this->hasDefault($key)) {
            return $this->getDefault($key);
        }

        return $this->filter->filterValue($value, $this->schema->getDefinition($key));
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
        $var = getenv($name);

        if (false === $var && $this->hasDefault($key)) {
            return true;
        }

        return false === $var
            ? false
            : $this->filter->validateValue($var, $this->schema->getDefinition($key));
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
