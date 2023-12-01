<?php

namespace Pnl\PnlDocker\Command;

use Pnl\App\AbstractCommand;
use Pnl\Application;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\PNLDocker\Services\DockerCommand;

class StartCommand extends AbstractCommand
{
    protected const NAME = "start";

    private string $currentPath;

    public function __construct(
        Application $app,
        private readonly DockerCommand $dockerCommand,
    )
    {
        $this->currentPath = $app->get('PWD');

        if ($this->currentPath === null) {
            throw new \RuntimeException('Could not determine current path');
        }
    }

    public static function getArguments(): ArgumentBag
    {
        $arg = new ArgumentBag();
        $arg->add('no-detach', false, 'No detach from the container', ArgumentType::BOOLEAN, false)
            ->add('force', false, 'Force the start of the container', ArgumentType::BOOLEAN, false)
            ->add('shy', false, 'Try starting the container without any change', ArgumentType::BOOLEAN, true)
            ->add('smart', false, 'Try to find new port to start the container', ArgumentType::BOOLEAN, false);

        return $arg;
    }

    public function getDescription(): string
    {
        return 'Starts the PNL Docker application';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $method = match (true) {
            $input->get('smart') => 'smart',
            $input->get('force') => 'force',
            $input->get('shy') => 'shy',
            default => 'shy'
        };

        $this->dockerCommand->up($this->currentPath, !$input->get('no-detach'), $method);
    }
}
