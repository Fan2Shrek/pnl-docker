<?php

namespace Pnl\PNLDocker\Event;

enum DockerEvent: string
{
    case UP = 'pnl.docker.up';

    case DOWN = 'pnl.docker.down';
}
