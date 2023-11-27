<?php

namespace Pnl\PnlDocker\Command;

use Iterator;
use Pnl\App\AbstractCommand;
use Pnl\Application;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\PNLDocker\Services\Docker;
use Pnl\PNLDocker\Services\DockerConfigFactory;
use Pnl\PNLDocker\Services\DockerContext;
use Symfony\Component\Yaml\Yaml;

class StartCommand extends AbstractCommand
{
    protected const NAME = "start";

    private string $currentPath;

    /**
     * @var array<string, DockerConfig>
     */
    private array $containerList = [];

    public function __construct(
        Application $app,
        private readonly DockerContext $dockerContext,
        private readonly Docker $docker,
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
        $this->containerList = $this->dockerContext->getContainersFrom($this->currentPath);

        $this->docker->up();
    }
}
