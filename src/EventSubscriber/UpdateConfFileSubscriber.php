<?php

namespace Pnl\PNLDocker\EventSubscriber;

use PNL\PNLDocker\PNLDocker;
use Pnl\PNLDocker\Event\DockerEvent;
use Pnl\PNLDocker\Event\DockerUpEvent;
use Pnl\PNLDocker\Services\DockerConfigFactory;
use Pnl\PNLDocker\Services\DockerContext;
use Pnl\PNLDocker\Services\VirtualDumper\VirtualDumper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Yaml\Yaml;

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
        ];
    }

    public function onStart(DockerUpEvent $event): void
    {
        $dockerConfig = $this->dockerContext->getContainersFrom($event->getPath());
        $fullConfig = require PNLDocker::getRegistrationFile();
        $fullConfig[$event->getPath()] = $dockerConfig;
        $content = $this->virtualDumper->dump($fullConfig);

        $file = fopen(PNLDocker::getRegistrationFile(), 'w');

        fwrite($file, "<?php\n\nreturn ");

        fwrite($file, $content);

        fwrite($file, ';');

        fclose($file);
    }
}
