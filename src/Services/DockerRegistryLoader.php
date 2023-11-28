<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfigBag;

class DockerRegistryLoader
{
    public function __construct(
        private readonly DockerConfigFactory $dockerConfigFactory,
    ) {
    }

    public function load(string $path, bool $asConfigBag = false): DockerConfigBag|array
    {
        $registry = require $path;

        $loadedRegistry = [];

        foreach ($registry as $project => $dockerConfig) {
            if ($asConfigBag) {
                $loadedRegistry[$project] = $this->dockerConfigFactory->createDockerBag($dockerConfig);
                continue;
            }
            $loadedRegistry[$project] = $this->dockerConfigFactory->createFromArray($dockerConfig);
        }

        return $loadedRegistry;
    }
}
