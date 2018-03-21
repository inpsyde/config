<?php
declare(strict_types = 1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Exception\MissingConfig;
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

    public function get(string $key)
    {
        if (! $this->get($key)) {
            throw new MissingConfig("Missing env config: '{$key}'");
        }
    }

    public function has(string $key) : bool
    {

        $name = $this->getName($key);
        if (!$name) {
            return false;
        }
        $var = getenv($name);

        return false === $var
            ? $var
            : $this->filter->validateVariable($var, $this->schema->getDefinition($key));
    }

    private function getName(string $key) : string
    {
        $definition = $this->schema->getDefinition($key);

        return empty($definition)
            ? ''
            : $definition['source_name'];
    }
}
