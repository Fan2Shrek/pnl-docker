<?php

namespace Pnl\PNLDocker\Services\Docker\Factory;

use Pnl\PNLDocker\Docker\DockerConfig;
use Pnl\PNLDocker\Docker\DockerConfigBag;

class DockerConfigFactory extends AbstractDockerConfig
{
    public function createFromArray(array $dockerConfigs): array
    {
        $dockerConfigObjects = [];
        foreach ($dockerConfigs as $dockerConfig) {
            $dockerConfigObjects[$dockerConfig['Names'][0]] = $this->create(
                $dockerConfig['Names'][0],
                $dockerConfig['Image'] ?? '',
                $this->convertPorts($dockerConfig['Ports']),
                $dockerConfig['State'] === 'running' ? true : false
            );
        }

        return $dockerConfigObjects;
    }

    private function convertPorts(array $ports): array
    {
        $convertedPorts = [];
        $donePort = [];

        foreach ($ports as $port) {
            if (!isset($port['PublicPort'])) {
                continue;
            }

            if (!in_array($port['PublicPort'], $donePort)) {
                $convertedPorts[] = $port;
            }

            $donePort[] = $port['PublicPort'];
        }

        return $convertedPorts;
    }
}
