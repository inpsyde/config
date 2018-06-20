<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Exception\MissingDefaultValue;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
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

    private $reader;

    /**
     * @param Schema $schema
     * @param Filter $filter
     */
    public function __construct(Schema $schema, Filter $filter = null, SchemaReader $reader = null)
    {
        $this->schema = $schema;
        $this->filter = $filter
            ?: new Filter();
        $this->reader = $reader
            ?: new SchemaReader();
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new MissingConfig("Missing env config: '{$key}'");
        }

        $name = $this->reader->sourceName($key, $this->schema);
        if (defined($name)) {
            return $this->filter->filterValue(
                constant($name),
                $this->schema->getDefinition($key)
            );
        }

        return $this->reader->defaultValue($key, $this->schema);
    }

    public function has(string $key): bool
    {
        $name = $this->reader->sourceName($key, $this->schema);
        if (! $name) {
            return false;
        }

        if (defined($name)) {
            return $this->filter->validateValue(
                constant($name),
                $this->schema->getDefinition($key)
            );
        }

        return $this->reader->hasDefault($key, $this->schema);
    }
}