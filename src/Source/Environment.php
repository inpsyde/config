<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\UnknownKey;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Helper\SchemaReader;
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
     * @var SchemaReader
     */
    private $reader;

    /**
     * @param Filter $filter
     * @param Schema $schema
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
     * @inheritdoc
     */
    public function get(string $key)
    {
        if (! $this->has($key)) {
            throw new UnknownKey("Missing env config: '{$key}'");
        }

        $name = $this->reader->sourceName($key, $this->schema);
        $value = getenv($name);
        if (false === $value && $this->reader->hasDefault($key, $this->schema)) {
            return $this->reader->defaultValue($key, $this->schema);
        }

        return $this->filter->filterValue($value, $this->schema->getDefinition($key));
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
        $var = getenv($name);

        if (false === $var && $this->reader->hasDefault($key, $this->schema)) {
            return true;
        }

        return false === $var
            ? false
            : $this->filter->validateValue($var, $this->schema->getDefinition($key));
    }
}
