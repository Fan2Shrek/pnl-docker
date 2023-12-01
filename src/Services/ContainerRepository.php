<?php

namespace Pnl\PNLDocker\Services;

class ContainerRepository
{
    public function __construct(
        private readonly DockerRegistryManager $dockerRegistryManager,
    ) {
    }

    public function findByPort(array $publicPort): ?array
    {
        $bags = $this->dockerRegistryManager->get();

        $containers = [];

        foreach ($bags as $bag) {
            foreach ($bag->getContainers() as $container) {
                if (!$container->isRunning()) {
                    continue;
                }

                if (array_intersect($publicPort, $container->getPublicPort())) {
                    $containers[] = $container;
                }
            }
        }

        return empty($containers) ? null : $containers;
    }
}
