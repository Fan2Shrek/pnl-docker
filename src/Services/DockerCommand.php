<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\Container;
use Pnl\PNLDocker\Docker\DockerConfigBag;
use Pnl\PNLDocker\Event\DockerUpEvent;
use Pnl\PNLDocker\Services\DockerRegistryManager;
use Pnl\PNLDocker\Services\Docker\Docker;
use Pnl\PNLDocker\Services\Docker\DockerContext;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DockerCommand
{
    private string $dockerExec;

    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
        private readonly DockerContext $dockerContext,
        private readonly Docker $docker,
        private readonly DockerRegistryManager $dockerRegistryManager,
        private readonly ContainerRepository $containerRepository,
        ?string $dockerExec = null,
    ) {
        $this->dockerExec = $dockerExec ?? $this->findDockerExec();;
    }

    public function up(string $currentPath, bool $detach = true, string $method = 'shy'): void
    {
        $this->dockerRegistryManager->refreshAllContainer();
        $isKnow = true;
        $bag = $this->dockerRegistryManager->getBagFrom($currentPath);

        if (null === $bag) {
            $isKnow = false;
            $bag = $this->dockerContext->getContainersFrom($currentPath);
        }

        if (empty($bag->getStopedContainers())) {
            return;
        }

        $pairsContainer = $this->findPairs($bag);

        if (empty($pairsContainer)) {
            if (!$isKnow) {
                $this->executeCommand('up', ['detach' => $detach]);
            } else {
                $this->docker->up($bag);
            }

            return;
        }

        switch ($method) {
            case 'force':
                foreach ($pairsContainer as $container) {
                    $this->docker->stop($container);
                }

                if (!$isKnow) {
                    $this->executeCommand('up', ['detach' => $detach]);
                } else {
                    $this->docker->up($bag);
                }

                break;
            case 'smart':
                $smartedBag = $this->doSmart($bag, $pairsContainer);
                $this->dockerContext->dumpDockerConfig($currentPath, $smartedBag);

                $this->executeCommand('up', ['detach' => $detach]);
                break;
            case 'shy':
                throw new \RuntimeException(sprintf('Couldn\'t start containers with %s method', $method));
            default:
                throw new \RuntimeException(sprintf('Method %s not supported', $method));
        }

        $this->eventDispatcher->dispatch(new DockerUpEvent($currentPath, $bag->getContainers()), DockerUpEvent::NAME);
    }

    private function findPairs(DockerConfigBag $bag): array
    {
        $pairs = [];
        foreach ($bag->getContainers() as $container) {
            $result = $this->containerRepository->findByPort($container->getPublicPort());

            if (null !== $result) {
                $pairs[$container->getContainerName()] = $result;
            }
        }

        return $pairs;
    }

    private function executeCommand(string $command, array $arg = [], bool $silent = false): void
    {
        $realCommand = sprintf(
            '%s \'%s\' %s %s %s',
            $silent ? 'nohup' : '',
            $this->dockerExec,
            $command,
            $this->parseArg($arg),
            $silent ? '&>/dev/null &' : ''
        );

        $this->doExecute($realCommand);
    }

    private function doExecute(string $command): void
    {
        exec($command);
    }

    private function parseArg(array $args = []): string
    {
        $parsedArgs = [];
        foreach ($args as $key => $value) {
            if (is_bool($value) && $value === true) {
                $parsedArgs[] = sprintf('--%s', $key);
                continue;
            }
            $parsedArgs[] = sprintf('--%s %s', $key, $value);
        }

        return implode(' ', $parsedArgs);
    }

    private function findDockerExec(): string
    {
        $dockerExec = exec('which docker-compose');

        if ($dockerExec === null) {
            throw new \RuntimeException('Could not find docker executable');
        }

        return $dockerExec;
    }

    private function doSmart(DockerConfigBag $bag, array $pairs): DockerConfigBag
    {
        foreach ($pairs as $containerName => $ports) {
            $container = $bag->getContainer($containerName);
            foreach ($ports as $portNumber => $_) {
                $this->changePort($container, (int) $portNumber);
            }
        }

        return $bag;
    }

    private function changePort(Container $container, int $port): void
    {
        $oldPort = $port;
        do {
            ++$port;
        } while (!empty($this->containerRepository->findByPort([$port])));
        $container->changePort($oldPort, $port);
    }
}
