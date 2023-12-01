<?php

namespace Pnl\PNLDocker\Event;

use Pnl\PNLDocker\Docker\Container;
use Symfony\Contracts\EventDispatcher\Event;

class DockerContainerEvent extends Event
{
    public function __construct(
        public readonly Container $container,
    ) {
    }
}
