<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfig;
use Pnl\PNLDocker\Docker\DockerConfigBag;

class DockerConfigFactory
{
    public function create(string $containerName, string $image, array $ports = []): DockerConfig
    {
        $dockerConfig = new DockerConfig(
            $containerName,
            $image,
            $ports,
        );

        return $dockerConfig;
    }

    public function createFromArray(array $dockerConfigs): array
    {
        $dockerConfigObjects = [];
        foreach ($dockerConfigs as $name => $dockerConfig) {
            if (!isset($dockerConfig['ports'])) {
                $ports = [];
            } elseif (array_is_list($dockerConfig['ports'])) {
                $ports = $this->convertPorts($dockerConfig['ports']);
            } else {
                $ports = $dockerConfig['ports'];
            }

            $dockerConfigObjects[$name] = $this->create(
                $name,
                $dockerConfig['image'] ?? '',
                $ports,
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

    private function convertPorts(array $ports): array
    {
        $convertedPorts = [];
        foreach ($ports as $port) {
            [$hostPort, $containerPort] = explode(':', $port);
            $convertedPorts[$containerPort][] = $hostPort;
        }

        return $convertedPorts;
    }
}
