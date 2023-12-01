<?php

namespace Pnl\PNLDocker\Services\Docker\Factory;

use Pnl\PNLDocker\Docker\Container;

interface DockerFactoryInterface
{
    public function create(string $id, string $containerName, string $image, array $ports, bool $isRunning = false): Container;

    public function createFromArray(array $dockerConfigs): array;
}
