<?php

namespace Pnl\PnlDocker\Command;

use Iterator;
use Pnl\App\AbstractCommand;
use Pnl\Application;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\PNLDocker\Services\DockerConfigFactory;
use Symfony\Component\Yaml\Yaml;

class StartCommand extends AbstractCommand
{
    protected const NAME = "start";

    private string $currentPath;

    private array $containerList = [];

    public function __construct(
        Application $app,
        private DockerConfigFactory $dockerConfigFactory
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
        foreach (new \DirectoryIterator($this->currentPath) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($fileInfo->isDir()) {
                continue;
            }

            $dockerFiles = [];
            if (!str_contains($fileInfo->getFilename(), 'docker-compose')) {
                continue;
            }

            $dockerFiles[] = $fileInfo->getPathname();
        }

        $this->readDockerFiles($dockerFiles);
        dd($this->containerList);
    }

    private function readDockerFiles(array $dockerFiles): void
    {
        foreach ($dockerFiles as $dockerFile) {
            $dockerFileContent = Yaml::parseFile($dockerFile);

            if (null === $dockerFileContent || !isset($dockerFileContent['services'])) {
                continue;
            }

            $this->containerList[] = $this->dockerConfigFactory->createFromArray($dockerFileContent['services']);
        }
    }
}
