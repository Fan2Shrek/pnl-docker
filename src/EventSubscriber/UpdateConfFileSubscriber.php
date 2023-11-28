<?php

namespace Pnl\PNLDocker\EventSubscriber;

use Pnl\PNLDocker\Event\DockerEvent;
use Pnl\PNLDocker\Event\DockerUpEvent;
use Pnl\PNLDocker\Services\DockerConfigFactory;
use Pnl\PNLDocker\Services\DockerContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Yaml\Yaml;

class UpdateConfFileSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly DockerContext $dockerContext,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DockerEvent::UP->value => 'onStart',
        ];
    }

    public function onStart(DockerUpEvent $event): void
    {
        $dockerConfig = $this->dockerContext->getContainersFrom($event->getPath());
        dd($dockerConfig);
    }
}
