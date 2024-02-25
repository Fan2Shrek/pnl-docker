<?php

namespace Pnl\PNLDocker\Event;

class DockerDownEvent
{
    public const NAME = DockerEvent::DOWN->value;

    private array $containers = [];

    public function __construct(
        private string $path,
        array $containers = [],
    ) {
        foreach ($containers as $container) {
            $container->start();
        }
        $this->containers = $containers;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }
}
