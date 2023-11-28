<?php

namespace Pnl\PNLDocker\Services\Docker;

class Docker
{
    public function __construct(
        private readonly DockerClient $dockerClient,
        private readonly DockerConfigFactory $dockerConfigFactory,
    ) {
    }

    public function getContainers(): array
    {
        $result = $this->dockerClient->getContainers();

        dd($this->dockerConfigFactory->createFromArray($result));
        return $this->dockerConfigFactory->create($result);
    }
}
