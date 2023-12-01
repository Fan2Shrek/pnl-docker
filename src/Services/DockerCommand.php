<?php

namespace Pnl\PNLDocker\Services;

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
        ?string $dockerExec = null,
    ) {
        $this->dockerExec = $dockerExec ?? $this->findDockerExec();;
    }

    public function up(string $currentPath, bool $detach = true, string $method = 'shy'): void
    {
        $this->docker->getContainers(true);
        $bag = $this->dockerRegistryManager->getBagFrom($currentPath);

        switch ($method) {
            case 'force':
                $this->docker->forceStart($bag->getContainers());
                break;
            case 'shy':
                $this->executeCommand('up', ['detach' => $detach]);
                break;
            default:
                throw new \RuntimeException(sprintf('Method %s not supported', $method));
        }
        $this->eventDispatcher->dispatch(new DockerUpEvent($currentPath, $bag->getContainers()), DockerUpEvent::NAME);
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
}
