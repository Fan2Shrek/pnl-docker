<?php

namespace Pnl\PNLDocker\EventSubscriber;

use Pnl\PNLDocker\Event\DockerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateConfFileSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        die('qsdqs');

        return [
            DockerEvent::UP->value => 'onStart',
        ];
    }

    public function onStart(): void
    {
        die('qsdqs');
    }
}
