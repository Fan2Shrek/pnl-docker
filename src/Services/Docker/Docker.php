<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\PNLDocker\Docker\Container;
use Pnl\PNLDocker\Docker\DockerConfigBag;
use Pnl\PNLDocker\Event\DockerContainerEvent;
use Pnl\PNLDocker\Event\DockerEvent;
use Pnl\PNLDocker\Event\DockerReadEvent;
use Pnl\PNLDocker\Services\Docker\Factory\ContainerFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Docker
{
    public function __construct(
        private readonly DockerClient $dockerClient,
        private readonly ContainerFactory $dockerConfigFactory,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function forceStart(array $containers): void
    {
        foreach ($containers as $container) {
            $this->start($container);
        }
    }

    public function up(DockerConfigBag $dockerConfigBag): void
    {
        foreach ($dockerConfigBag->getContainers() as $container) {
            $this->start($container);
        }
    }

    public function start(Container $container): void
    {
        $this->dockerClient->start($container);
        $this->eventDispatcher->dispatch(new DockerContainerEvent($container), DockerEvent::START->value);
    }

    public function stop(Container $container): void
    {
        $this->dockerClient->stop($container);
        $this->eventDispatcher->dispatch(new DockerContainerEvent($container), DockerEvent::STOP->value);
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
