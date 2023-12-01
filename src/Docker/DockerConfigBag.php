<?php

namespace Pnl\PNLDocker\Docker;

class DockerConfigBag
{
    private array $containers = [];

    public function __construct(array $containers = [])
    {
        $this->containers = $containers;
    }

    public function addContainer(DockerConfig $dockerConfig): void
    {
        $this->containers[$dockerConfig->getContainerName()] = $dockerConfig;
    }

    public function getContainer(string $containerName): DockerConfig
    {
        return $this->containers[$containerName];
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

    public function getImages(string $image): array
    {
        return array_filter(
            $this->containers,
            fn (DockerConfig $dockerConfig) => str_contains($dockerConfig->getImage(), $image)
        );
    }
}
