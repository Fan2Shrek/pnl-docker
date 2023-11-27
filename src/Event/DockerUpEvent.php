<?php

namespace Pnl\PNLDocker\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DockerUpEvent extends Event
{
    public const NAME = DockerEvent::UP->value;

    public function __construct(
        private string $path,
        private array $containers = [],
    ) {
    }
}
