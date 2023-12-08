<?php

namespace Pnl\PNLDocker\Command;

use PNL\PNLDocker\PNLDocker;
use Pnl\App\AbstractCommand;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\PNLDocker\Services\DockerRegistryManager;
use Pnl\PNLDocker\Services\Docker\Docker;

class GoToCommand extends AbstractCommand
{
    protected const NAME = 'goto';

    private array $currentConfig = [];

    public function __construct(
        private readonly DockerRegistryManager $dockerRegistryLoader,
        private Docker $docker,
    ) {
    }

    public static function getArguments(): ArgumentBag
    {
        $arg = new ArgumentBag();

        $arg->add('project', true, nameless: true);
        $arg->add('image', false, 'Images to get', ArgumentType::STRING, 'nginx');

        return $arg;
    }

    public function getDescription(): string
    {
        return 'Go to command';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $this->currentConfig = $this->dockerRegistryLoader->get(true);

        if (!$input->haveNameless()) {
            throw new \Exception('You must provide a project name');
        }

        $project = $input->getNameless();

        $dirs = array_keys($this->currentConfig);

        foreach ($dirs as $dir) {
            $exploded = explode('/', $dir);
            $projectName = end($exploded);

            if ($projectName === $project) {
                $this->doInvoke($input, $output, $dir);
                return;
            }

            if (levenshtein($projectName, $project) < 3) {
                $this->doInvoke($input, $output, $dir);
                return;
            }
        }

        throw new \Exception(sprintf('The project %s does not exist', $project));
    }

    private function doInvoke(InputInterface $input, OutputInterface $output, string $dir): void
    {
        $output->writeln(sprintf('Going to %s', $dir));

        $bag = $this->currentConfig[$dir];
        $containers = $bag->getImages($input->get('image'));

        if (empty($containers)) {
            throw new \Exception(sprintf('No container found for image %s', $input->get('image')));
        }

        foreach ($containers as $container) {
            $output->writeln(sprintf(' - %s : %s', $container->getContainerName(), $this->convertPort($container->getPorts())));
        }

        $output->writeln('');
    }

    private function convertPort(array $port): string
    {
        $urls = '';

        foreach ($port as $host) {
            $urls .= sprintf("http://locahost:%d \t", $host['PublicPort']);
        }

        return $urls;
    }
}
