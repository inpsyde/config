<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\UnknownKey;

interface Config
{

    /**
     * @throws UnknownKey
     * @return mixed
     */
    public function get(string $key);

    public function has(string $key): bool;
}
