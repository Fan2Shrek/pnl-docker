<?php

namespace Pnl\PNLDocker\Services\Docker\Factory;

class DockerFileConfigFactory extends AbstractDockerConfig
{
    public function createFromArray(array $dockerConfigs): array
    {
        $dockerConfigObjects = [];

        foreach ($dockerConfigs as $containerName => $dockerConfig) {
            $dockerConfigObjects[$containerName] = $this->create(
                '',
                $dockerConfig['name'],
                $dockerConfig['image'] ?? '',
                $this->convertPorts($dockerConfig['ports'] ?? [])
            );
        }

        return $dockerConfigObjects;
    }

    private function convertPorts(array $ports): array
    {
        $portsArray = [];

        foreach ($ports as $port) {
            $exploded = explode(':', $port);

            $portsArray[] = [
                "PublicPort" => $exploded[0],
                "PrivatePort" => $exploded[1],
            ];
        }

        return $portsArray;
    }
}
