<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\MissingConfig;

final class ConfigComposition implements Config
{

    /**
     * @var Config[]
     */
    private $components = [];

    /**
     * @param Config[] $components
     */
    public function __construct(array $components)
    {
        $this->components = $components;
    }

    public function get(string $key)
    {
        if (! array_key_exists($key, $this->components)){
            throw new MissingConfig("No configuration configured for '{$key}'");
        }

        return $this->components[$key]
            ->get($key);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->components)
            ? $this->components[$key]->has($key)
            : false;
    }
}