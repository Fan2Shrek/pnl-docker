<?php

namespace Pnl\PNLDocker\EventSubscriber;

use PNL\PNLDocker\PNLDocker;
use Pnl\PNLDocker\Event\DockerEvent;
use Pnl\PNLDocker\Event\DockerUpEvent;
use Pnl\PNLDocker\Services\Docker\DockerContext;
use Pnl\PNLDocker\Services\VirtualDumper\VirtualDumper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateConfFileSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly DockerContext $dockerContext,
        private readonly VirtualDumper $virtualDumper,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DockerEvent::UP->value => 'onStart',
            DockerEvent::UP->value => 'onStart',
        ];
    }

    public function onStart(DockerUpEvent $event): void
    {
        $fullConfig = require PNLDocker::getRegistrationFile();

        // If file is empty
        if (1 === $fullConfig) {
            $fullConfig = [];
        }

        $fullConfig[$event->getPath()] = $event->getContainers();
        $content = $this->virtualDumper->dump($fullConfig);

        $file = fopen(PNLDocker::getRegistrationFile(), 'w');

        fwrite($file, sprintf("<?php\n\nreturn %s;", $content));

        fclose($file);
    }
}
