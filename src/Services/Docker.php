<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Event\DockerUpEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Docker
{
    private string $dockerExec;

    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
        ?string $dockerExec = null,
    )
    {
        $this->dockerExec = $dockerExec ?? $this->findDockerExec();;
    }

    public function up(string $currentPath, bool $detach = true): void
    {
        $this->executeCommand('up', ['detach' => $detach]);
        $this->eventDispatcher->dispatch(new DockerUpEvent($currentPath));
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
