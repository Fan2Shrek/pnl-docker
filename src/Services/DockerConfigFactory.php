<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfig;

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
            $dockerConfigObjects[$name] = $this->create(
                $name,
                $dockerConfig['image'],
                $this->convertPorts($dockerConfig['ports'] ?? []),
            );
        }

        return $dockerConfigObjects;
    }

    private function convertPorts(array $ports): array
    {
        $convertedPorts = [];
        foreach ($ports as $port) {
            [$hostPort, $containerPort] = explode(':', $port);
            $convertedPorts[$containerPort] = $hostPort;
        }

        return $convertedPorts;
    }
}
