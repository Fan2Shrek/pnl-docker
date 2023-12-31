<?php

namespace Pnl\PNLDocker\Docker;

class DockerConfigBag
{
    private array $containers = [];

    public function __construct(array $containers = [])
    {
        $this->containers = $containers;
    }

    public function addContainer(Container $container): void
    {
        $this->containers[$container->getContainerName()] = $container;
    }

    public function getContainer(string $containerName): Container
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
            fn (Container $container) => str_contains($container->getImage(), $image)
        );
    }

    public function getStopedContainers(): array
    {
        return array_filter(
            $this->containers,
            fn (Container $container) => !$container->isRunning()
        );
    }

    public function getContainerByPublicPort(string $port): ?Container
    {
        foreach ($this->containers as $container) {
            if ($container->getPublicPort() === $port) {
                return $container;
            }
        }

        return null;
    }
}
