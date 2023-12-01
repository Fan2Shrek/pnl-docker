<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfigBag;

class DockerRegistryLoader
{
    public function __construct(
    ) {
    }

    public function load(string $path): DockerConfigBag|array
    {
        $registry = require $path;

        return $registry;
    }
}
