<?php

namespace Pnl\PNLDocker\EventSubscriber;

use PNL\PNLDocker\PNLDocker;
use Pnl\PNLDocker\Event\DockerEvent;
use Pnl\PNLDocker\Event\DockerReadEvent;
use Pnl\PNLDocker\Event\DockerUpEvent;
use Pnl\PNLDocker\Services\DockerRegistryManager;
use Pnl\PNLDocker\Services\Docker\Docker;
use Pnl\PNLDocker\Services\Docker\DockerContext;
use Pnl\PNLDocker\Services\VirtualDumper\VirtualDumper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateConfFileSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Docker $docker,
        private readonly DockerContext $dockerContext,
        private readonly VirtualDumper $virtualDumper,
        private readonly DockerRegistryManager $dockerRegistryManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DockerEvent::UP->value => 'onStart',
            DockerEvent::READ->value => 'onRead',
        ];
    }

    public function onStart(DockerUpEvent $event): void
    {
        $this->dockerRegistryManager->save();
    }

    public function onRead(DockerReadEvent $event): void
    {
        $this->dockerRegistryManager->update($event->getContainers());
    }
}
