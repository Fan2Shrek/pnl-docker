<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\PNLDocker\Docker\DockerConfig;
use Pnl\PNLDocker\Docker\DockerConfigBag;

class DockerConfigFactory
{
    public function create(string $containerName, string $image, array $ports = [], bool $isRunning = false): DockerConfig
    {
        $dockerConfig = new DockerConfig(
            $containerName,
            $image,
            $ports,
            $isRunning
        );

        return $dockerConfig;
    }

    public function createFromArray(array $dockerConfigs): array
    {
        $dockerConfigObjects = [];
        foreach ($dockerConfigs as $dockerConfig) {
            $dockerConfigObjects[$dockerConfig['Names'][0]] = $this->create(
                $dockerConfig['Names'][0],
                $dockerConfig['Image'] ?? '',
                $dockerConfig['Ports'],
                $dockerConfig['State'] === 'running' ? true : false
            );
        }

        return $dockerConfigObjects;
    }

    public function update(DockerConfig $dockerConfig, ?DockerConfig $oldDockerConfig): DockerConfig
    {
        if (null === $oldDockerConfig) {
            return $dockerConfig;
        }

        $dockerConfig->addPorts($oldDockerConfig->getPorts());

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
