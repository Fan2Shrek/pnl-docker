<?php

namespace Pnl\PNLDocker\Event;

enum DockerEvent: string
{
    case UP = 'pnl.docker.up';

    case DOWN = 'pnl.docker.down';

    case READ = 'pnl.docker.read';

    case START = 'pnl.docker.start';

    case STOP = 'pnl.docker.stop';
}
