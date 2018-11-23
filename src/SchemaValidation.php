<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\InvalidSchema;
use Inpsyde\Config\Source\Source;

class SchemaValidation
{

    private $schema = [];

    public function validateSchema(array $schema): Schema
    {
        $this->schema = [];
        array_walk(
            $schema,
            function (...$args) {
                $this->validateKey(...$args);
            }
        );

        return new Schema($this->schema);
    }

    /**
     * @throws InvalidSchema
     */
    private function validateKey($definition, $key)
    {
        if (! is_string($key)) {
            throw new InvalidSchema('Schema must be an associative array');
        }

        if (! is_array($definition)) {
            throw new InvalidSchema('Key definition must be an array');
        }

        $definition = $this->ensureRequiredDefinition($definition, $key);
        $definition = $this->validateFilter($definition, $key);
        $this->schema[$key] = $definition;
    }

    /**
     * @throws InvalidSchema
     */
    private function ensureRequiredDefinition(array $definition, string $key): array
    {
        $requireField = function ($field) use ($definition, $key) {
            if (! array_key_exists($field, $definition)) {
                throw new InvalidSchema(
                    "Missing definition '{$field}' for key '{$key}'"
                );
            }
        };
        $requireField('source');
        if (Source::SOURCE_VARIABLE !== $definition['source']) {
            $requireField('sourceName');

            return $definition;
        }

        $definition['sourceName'] = null;

        return $definition;
    }

    /**
     * @throws InvalidSchema
     */
    private function validateFilter(array $definition, string $key): array
    {
        if (! array_key_exists('filter', $definition) || null === $definition['filter']) {
            $definition['filter'] = null;

            return $definition;
        }

        if (is_callable($definition['filter'])) {
            return $definition;
        }

        if (! is_int($definition['filter'])) {
            throw new InvalidSchema("Filter must be either callable or integer for key '{$key}'");
        }

        $availableFilters = [
            FILTER_VALIDATE_BOOLEAN,
            FILTER_VALIDATE_EMAIL,
            FILTER_VALIDATE_FLOAT,
            FILTER_VALIDATE_INT,
            FILTER_VALIDATE_IP,
            FILTER_VALIDATE_REGEXP,
            FILTER_VALIDATE_URL,
            FILTER_SANITIZE_EMAIL,
            FILTER_SANITIZE_ENCODED,
            FILTER_SANITIZE_MAGIC_QUOTES,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_SANITIZE_NUMBER_INT,
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_SANITIZE_STRING,
            FILTER_SANITIZE_STRIPPED,
            FILTER_UNSAFE_RAW,
        ];

        if (! in_array($definition['filter'], $availableFilters, true)) {
            throw new InvalidSchema("Invalid filter for key '{$key}'");
        }

        return $definition;
    }
}
