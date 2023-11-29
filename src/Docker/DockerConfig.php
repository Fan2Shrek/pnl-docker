<?php

namespace Pnl\PNLDocker\Docker;

class DockerConfig
{
    public function __construct(
        private string $containerName,
        private string $image,
        private array $ports,
        private bool $isRunning = false,
    ) {
    }

    public function getContainerName(): string
    {
        return $this->containerName;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getPorts(): array
    {
        return $this->ports;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }

    public function addPorts(array $ports): void
    {
        foreach ($ports as $containerPort => $hostport) {
            foreach ($hostport as $port) {
                $this->ports[$containerPort][] = $port;
            }
        }
    }
}
