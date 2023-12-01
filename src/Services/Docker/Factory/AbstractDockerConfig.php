<?php

namespace Pnl\PNLDocker\Services\Docker\Factory;

use Pnl\PNLDocker\Docker\DockerConfig;
use Pnl\PNLDocker\Docker\DockerConfigBag;

abstract class AbstractDockerConfig implements DockerFactoryInterface
{
    public function create(string $id, string $containerName, string $image, array $ports = [], bool $isRunning = false): DockerConfig
    {
        $dockerConfig = new DockerConfig(
            $id,
            $containerName,
            $image,
            $ports,
            $isRunning
        );

        return $dockerConfig;
    }

    public function createDockerBag(array $containers): DockerConfigBag
    {
        $bag = new DockerConfigBag();

        foreach ($this->createFromArray($containers) as $container) {
            $bag->addContainer($container);
        }

        return $bag;
    }
}
