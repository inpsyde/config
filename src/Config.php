<?php
declare(strict_types=1);

namespace Inpsyde\Config;

interface Config
{

    /**
     * @return mixed
     */
    public function get(string $key);

    public function has(string $key): bool;
}
