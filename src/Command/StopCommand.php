<?php

namespace Pnl\PNLDocker\Command;

use Pnl\App\AbstractCommand;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\Style\CustomStyle;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\PNLDocker\EventSubscriber\LogContainerSubsriber;
use Pnl\Application;
use Pnl\Console\Input\ArgumentBag;
use Pnl\PNLDocker\Services\DockerCommand;

class StopCommand extends AbstractCommand
{
    protected const NAME = 'stop';

    public function __construct(
        private readonly DockerCommand $dockerCommand,
    ) {
    }

    public static function getArguments(): ArgumentBag
    {
        $arguments = new ArgumentBag();

        $arguments->add('project', true, 'The project to stop', nameless: true);

        return $arguments;
    }

    public function getDescription(): string
    {
        return 'Stop a project';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $style = new CustomStyle($output);

        $style->createStyle('green')
            ->setColor(TextColors::GREEN);

        $style->createStyle('white')
            ->setColor(TextColors::WHITE);

        LogContainerSubsriber::setStyle($style);

        $this->dockerCommand->down($input->get('project'));
    }
}
