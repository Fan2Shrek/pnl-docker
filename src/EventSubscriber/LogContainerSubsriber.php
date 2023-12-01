<?php

namespace Pnl\PNLDocker\EventSubscriber;

use Pnl\Console\Output\Style\CustomStyle;
use Pnl\PNLDocker\Event\DockerContainerEvent;
use Pnl\PNLDocker\Event\DockerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogContainerSubsriber implements EventSubscriberInterface
{
    private static CustomStyle $style;

    public static function setStyle(CustomStyle $style): void
    {
        self::$style = $style;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            DockerEvent::START->value => 'onContainerStart',
            DockerEvent::STOP->value => 'onContainerStop',
        ];
    }

    public function onContainerStart(DockerContainerEvent $event): void
    {
        self::$style->writeWithStyle('Starting container ', 'white');
        self::$style->writeWithStyle($event->container->getContainerName(), 'green');
        self::$style->writeWithStyle('...', 'white');
        self::$style->writeln('');
    }

    public function onContainerStop(DockerContainerEvent $event): void
    {
        self::$style->writeWithStyle('Stoping container ', 'white');
        self::$style->writeWithStyle($event->container->getContainerName(), 'green');
        self::$style->writeWithStyle('...', 'white');
        self::$style->writeln('');
    }
}
