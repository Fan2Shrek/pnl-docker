<?php

namespace Pnl\PnlDocker\Command;

use Pnl\App\AbstractCommand;
use Pnl\Application;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;

class StartCommand extends AbstractCommand
{
    protected const NAME = "start";

    private string $currentPath;

    public function __construct(Application $app)
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
        dd($this->currentPath);
    }
}
