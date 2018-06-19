<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Exception\MissingDefaultValue;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Schema;

final class Constant implements Source
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
     * @param Schema $schema
     * @param Filter $filter
     */
    public function __construct(Schema $schema, Filter $filter = null)
    {
        $this->schema = $schema;
        $this->filter = $filter
            ?: new Filter();
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new MissingConfig("Missing env config: '{$key}'");
        }

        $name = $this->getName($key);
        if(defined($name)) {
            return $this->filter->filterValue(
                constant($name),
                $this->schema->getDefinition($key)
            );
        }

        return $this->getDefault($key);
    }

    public function has(string $key): bool
    {
        $name = $this->getName($key);
        if (! $name) {
            return false;
        }

        if (defined($name)) {
            return $this->filter->validateValue(
                constant($name),
                $this->schema->getDefinition($key)
            );
        }

        return $this->hasDefault($key);
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