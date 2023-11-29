<?php

namespace Pnl\PNLDocker\DependencyInjection;

use Pnl\PNLDocker\Services\VirtualDumper\VirtualDumper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class VirtualDumperCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(VirtualDumper::class);
        $taggedServices = $container->findTaggedServiceIds('pnl.pnl_docker.virtual_dumper');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addDumper', [new Reference($id)]);
        }
    }
}
