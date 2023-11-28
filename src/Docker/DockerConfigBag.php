<?php

namespace Pnl\PNLDocker\Docker;

class DockerConfigBag
{
    private array $containers = [];

    public function addContainer(DockerConfig $dockerConfig): void
    {
        $this->containers[$dockerConfig->getContainerName()] = $dockerConfig;
    }

    public function getImages(string $image): array
    {
        return array_filter(
            $this->containers,
            fn (DockerConfig $dockerConfig) => str_contains($dockerConfig->getImage(), $image)
        );
    }
}
