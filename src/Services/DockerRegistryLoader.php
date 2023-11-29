<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfigBag;

class DockerRegistryLoader
{
    public function __construct(
    ) {
    }

    public function load(string $path, bool $asConfigBag = false): DockerConfigBag|array
    {
        $registry = require $path;

        if ($asConfigBag) {
            foreach ($registry as $dir => $dockerConfigs) {
                $loadedRegistry[$dir] = new DockerConfigBag();
                foreach ($dockerConfigs as $dockerConfig) {
                    $loadedRegistry[$dir]->addContainer($dockerConfig);
                }
            }
        }

        return $loadedRegistry;
    }
}
