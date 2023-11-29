<?php

namespace Pnl\PnlDocker\Command;

use Iterator;
use Pnl\App\AbstractCommand;
use Pnl\Application;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\PNLDocker\Services\DockerCommand;
use Pnl\PNLDocker\Services\Docker\DockerContext;

class StartCommand extends AbstractCommand
{
    protected const NAME = "start";

    private string $currentPath;

    public function __construct(
        Application $app,
        private readonly DockerContext $dockerContext,
        private readonly DockerCommand $dockerCommand
    )
    {
        $this->currentPath = $app->get('PWD');

        if ($this->currentPath === null) {
            throw new \RuntimeException('Could not determine current path');
        }
    }

    public function getDescription(): string
    {
        return 'Starts the PNL Docker application';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $this->dockerCommand->up($this->currentPath);
    }
}
