<?php

namespace PNL\PNLDocker;

use Pnl\Extensions\AbstractExtension;
use Pnl\PNLDocker\DependencyInjection\EventSubscriberCompiler;
use Pnl\PNLDocker\DependencyInjection\VirtualDumperCompiler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PNLDocker extends AbstractExtension
{
    protected static string $name = "docker";

    private const REGISTRATION_FILE = __DIR__ . '/../config/registration.php';

    public function getCommandTag(): string
    {
        return 'pnldocker-command';
    }

    public function prepareContainer(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');

        $container->addCompilerPass(new EventSubscriberCompiler());
        $container->addCompilerPass(new VirtualDumperCompiler());
    }

    public static function getRegistrationFile(): string
    {
        if (!file_exists(self::REGISTRATION_FILE)) {
            touch(self::REGISTRATION_FILE);
        }

        return self::REGISTRATION_FILE;
    }
}
