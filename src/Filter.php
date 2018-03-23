<?php
declare(strict_types=1);

namespace Inpsyde\Config;

class Filter
{

    /**
     * @param mixed $variable
     * @param array $schema
     *
     * @return mixed
     */
    public function filterValue($variable, array $schema)
    {
        if (! $schema['filter']) {
            return $variable;
        }

        if (is_callable($schema['filter'])) {
            return $schema['filter']($variable, $schema);
        }

        return filter_var($variable, $schema['filter']);
    }

    /**
     * @param mixed $variable
     * @param array $schema
     *
     * @return bool
     */
    public function validateValue($variable, array $schema): bool
    {
        try {
            $variable = $this->filterValue($variable, $schema);

            return false !== $variable || FILTER_VALIDATE_BOOLEAN !== $schema['filter'];
        } catch (\Throwable $e) {
            return false;
        }
    }
}
