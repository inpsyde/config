<?php
declare(strict_types = 1);

namespace Inpsyde\Config;

class Filter
{

    public function filterVariable($variable, array $schema)
    {

        if (! $schema[ 'filter' ]) {
            return $variable;
        }

        $filterDefinition = $schema[ 'filter' ];
        $filterOptions = $filterDefinition[ 'filter_options' ];

        if ($filterDefinition[ 'filter_flags' ]) {
            $filterOptions[ 'flags' ] = $filterDefinition[ 'filter_flags' ];
        }
        if ($filterDefinition[ 'filter_cb' ]) {
            $filterOptions[ 'options' ] = $filterDefinition[ 'filter_cb' ];
        }

        return filter_var($variable, $filterDefinition[ 'filter' ], $filterOptions);
    }

    public function validateVariable($variable, array $schema) : bool
    {
        if (FILTER_CALLBACK === $schema['filter']['filter']) {
            return true;
        }

        $variable = $this->filterVariable($variable, $schema);

        return false !== $variable || FILTER_VALIDATE_BOOLEAN !== $schema[ 'filter' ][ 'filter' ];
    }
}
