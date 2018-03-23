<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\InvalidSchema;

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

        if (array_key_exists($key, $this->schema)) {
            throw new InvalidSchema("Duplicate definition for key '{$key}'");
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
        foreach (['source', 'source_name'] as $requiredDefinition) {
            if (! array_key_exists($requiredDefinition, $definition)) {
                throw new InvalidSchema(
                    "Missing definition '{$requiredDefinition}' for key '{$key}''"
                );
            }
        }

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

        $filterDefinition = &$definition['filter'];
        if (! array_key_exists('filter', $filterDefinition)) {
            throw new InvalidSchema("Missing 'filter' for key {$key}");
        }

        if (array_key_exists('filter_options', $filterDefinition)) {
            if (! is_array($filterDefinition['filter_options'])) {
                throw new InvalidSchema("filter_options must be an array for key '{$key}'");
            }
        } else {
            $filterDefinition['filter_options'] = [];
        }

        if (! array_key_exists('filter_flags', $filterDefinition)) {
            $filterDefinition['filter_flags'] = null;
        }

        $availableFilters = [
            FILTER_CALLBACK,
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

        if (! in_array($filterDefinition['filter'], $availableFilters, true)) {
            throw new InvalidSchema("Filter is not valid for key '{$key}'");
        }

        if (FILTER_CALLBACK === $filterDefinition['filter']) {
            if (! array_key_exists('filter_cb', $filterDefinition)) {
                throw new InvalidSchema("Missing 'filter_cb' for key '{$key}'");
            }
            if (! is_callable($filterDefinition['filter_cb'])) {
                throw new InvalidSchema("Filter callback is not callable for key '{$key}'");
            }
        } else {
            $filterDefinition['filter_cb'] = null;
        }

        $availableFilterFlags = [
            FILTER_FLAG_STRIP_LOW,
            FILTER_FLAG_STRIP_HIGH,
            FILTER_FLAG_ALLOW_FRACTION,
            FILTER_FLAG_ALLOW_THOUSAND,
            FILTER_FLAG_ALLOW_SCIENTIFIC,
            FILTER_FLAG_NO_ENCODE_QUOTES,
            FILTER_FLAG_ENCODE_LOW,
            FILTER_FLAG_ENCODE_HIGH,
            FILTER_FLAG_ENCODE_AMP,
            FILTER_NULL_ON_FAILURE,
            FILTER_FLAG_ALLOW_OCTAL,
            FILTER_FLAG_ALLOW_HEX,
            FILTER_FLAG_IPV4,
            FILTER_FLAG_IPV6,
            FILTER_FLAG_NO_PRIV_RANGE,
            FILTER_FLAG_NO_RES_RANGE,
            FILTER_FLAG_PATH_REQUIRED,
            FILTER_FLAG_QUERY_REQUIRED,
        ];

        // Todo: allow bitwise disjunction of multiple flags
        if (! null === $filterDefinition['filter_flags']
            && ! in_array(
                $filterDefinition['filter_flags'],
                $availableFilterFlags,
                true
            )) {
            throw new InvalidSchema("Unknown filter_flag for key '{$key}'");
        }

        return $definition;
    }
}
