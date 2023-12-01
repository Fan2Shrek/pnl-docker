<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\PNLDocker\Docker\DockerConfigBag;
use Pnl\PNLDocker\Event\DockerReadEvent;
use Pnl\PNLDocker\Services\Docker\Factory\DockerConfigFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Docker
{
    public function __construct(
        private readonly DockerClient $dockerClient,
        private readonly DockerConfigFactory $dockerConfigFactory,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function forceStart(array $containers): void
    {
        foreach ($containers as $container) {
            /**
             * @todo close container running on port
             */
            $this->dockerClient->start($container->getId());
        }
    }

    public function getContainers(bool $asDockerBag = false): array
    {
        if ($asDockerBag) {
            $bags = $this->getDockerBags();

            $this->eventDispatcher->dispatch(new DockerReadEvent($bags), DockerReadEvent::NAME);

            return $bags;
        }

        $containers = [$this->dockerConfigFactory->createFromArray($this->fetchContainer())];

        $this->eventDispatcher->dispatch(new DockerReadEvent($containers), DockerReadEvent::NAME);

        return $containers;
    }

    public function getDockerBags(): array
    {
        $containers = $this->fetchContainer();

        $dockerBags = [];
        foreach ($containers as $container) {
            if (!isset($container['Labels']['com.docker.compose.project.working_dir'])) {
                $dockerBags[] = $this->dockerConfigFactory->createFromArray([$container]);

                continue;
            }

            $projectPath = $container['Labels']['com.docker.compose.project.working_dir'];
            if (!isset($dockerBags[$projectPath])) {
                $dockerBags[$projectPath] = new DockerConfigBag();
            }

            $dockerConfig = $this->dockerConfigFactory->createFromArray([$container]);
            $dockerBags[$projectPath]->addContainer(...array_values($dockerConfig));
        }

        return $dockerBags;
    }

    private function fetchContainer(): array
    {
        return $this->dockerClient->getContainers();
    }
}
