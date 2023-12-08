<?php

namespace Pnl\PNLDocker\Command;

use Pnl\App\AbstractCommand;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\Style\CustomStyle;
use Pnl\PNLDocker\Docker\DockerConfigBag;
use Pnl\PNLDocker\Services\DockerRegistryManager;

class ListCommand extends AbstractCommand
{
    protected const NAME = 'list';

    private CustomStyle $style;

    public function __construct(
        private readonly DockerRegistryManager $dockerRegistryManager,
    ) {
    }

    public function getDescription(): string
    {
        return 'List all project';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $registry = $this->dockerRegistryManager->get(true);

        $style = new CustomStyle($output);

        $style->createStyle('path')
            ->setColor(TextColors::MAGENTA);

        $style->createStyle('image')
            ->setColor(TextColors::BLUE);

        $this->style = $style;

        foreach ($registry as $path => $bag) {
            $this->printBag($path, $bag);
        }
        // dd($registry);
    }

    private function printBag(string $path, DockerConfigBag $bag): void
    {
        $folder = explode('/', $path);

        $this->style->writeWithStyle(sprintf("%s: \n", end($folder)), 'path');

        foreach ($bag->getContainers() as $container) {
            $this->style->writeWithStyle(sprintf("\t - %s\n", $container->getImage()), 'image');
        }
    }
}
