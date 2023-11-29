<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\PNLDocker\Event\DockerReadEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Docker
{
    public function __construct(
        private readonly DockerClient $dockerClient,
        private readonly DockerConfigFactory $dockerConfigFactory,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function getContainers(): array
    {
        $result = $this->dockerClient->getContainers();
        dd($result);
        $containers = [$this->dockerConfigFactory->createFromArray($result)];

        $this->eventDispatcher->dispatch(new DockerReadEvent($containers));

        return $containers;
    }

    public function getDockerBags(): array
    {
        $containers = $this->getContainers();

        $dockerBags = [];
        foreach ($containers as $container) {
            if (isset($dockerBags[$container['Labels']['com.docker.compose.project']])) {
                $dockerBags[$container->getPath()]->addContainer($container);
                continue;
            }
            $dockerBags[$container->getPath()] = $this->dockerConfigFactory->createDockerBag($container->getContainers());
        }

        return $dockerBags;
    }
}
