<?php

namespace App\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GithubFollowbacksCommand extends Command
{
    public const COMMAND_NAME = 'github:followbacks';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Given a github user, return how many followers is the user following back');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Test command is working' . PHP_EOL;

        return 0;
    }
}
