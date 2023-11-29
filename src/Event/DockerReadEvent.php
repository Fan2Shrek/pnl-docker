<?php

namespace Pnl\PNLDocker\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DockerReadEvent extends Event
{
    public const NAME = DockerEvent::READ->value;

    public function __construct(
        private array $containers = [],
    ) {
    }

    public function getContainers(): array
    {
        return $this->containers;
    }
}
