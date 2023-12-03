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

                $ports = array_intersect($publicPort, $container->getPublicPort());

                if (!empty($ports)) {
                    foreach ($ports as $port) {
                        $containers[$port] = $container;
                    }
                }
            }
        }

        return empty($containers) ? null : $containers;
    }
}
