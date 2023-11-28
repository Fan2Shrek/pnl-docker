<?php

namespace Pnl\PNLDocker\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EventSubscriberCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $eventDispatcher = $container->getDefinition(EventDispatcher::class);

        foreach ($container->getServiceIds() as $id) {
            try {
                $serviceDefinition = $container->getDefinition($id);
                $class = $serviceDefinition->getClass();

                if ($class && class_exists($class)) {
                    $reflection = new \ReflectionClass($class);

                    if ($reflection->implementsInterface(EventSubscriberInterface::class)) {
                        $eventDispatcher->addMethodCall('addSubscriber', [new Reference($id)]);
                    }
                }
            } catch (ServiceNotFoundException $e) {
                continue;
            }
        }
    }
}
