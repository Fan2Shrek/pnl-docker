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
                $dockerConfig['Ports'],
                $dockerConfig['State'] === 'running' ? true : false
            );
        }

        return $dockerConfigObjects;
    }
}
