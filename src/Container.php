<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\MissingConfig;

final class Container implements Config
{

    /**
     * @var Config[]
     */
    private $sources = [];

    /**
     * Expects a parameter in [ $key => Config $config ] format
     *
     * @param Config[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function get(string $key)
    {
        if (! array_key_exists($key, $this->sources)){
            throw new MissingConfig("No configuration configured for '{$key}'");
        }

        return $this->sources[$key]
            ->get($key);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->sources)
            ? $this->sources[$key]->has($key)
            : false;
    }
}