<?php

namespace Pnl\PNLDocker\Services\Docker\Factory;

use Pnl\PNLDocker\Docker\DockerConfig;

interface DockerFactoryInterface
{
    public function create(string $containerName, string $image, array $ports, bool $isRunning = false): DockerConfig;

    public function createFromArray(array $dockerConfigs): array;
}
