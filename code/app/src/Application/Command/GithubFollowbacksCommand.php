<?php

namespace App\Application\Command;

use App\Domain\Service\GithubUserFollowbacksService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GithubFollowbacksCommand extends Command
{
    public const COMMAND_NAME = 'github:followbacks';
    private const OPTION_USERNAME = 'username';

    private GithubUserFollowbacksService $githubUserFollowbacksService;

    public function __construct(GithubUserFollowbacksService $githubUserFollowbacksService)
    {
        $this->githubUserFollowbacksService = $githubUserFollowbacksService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Given a github user, return how many followers is the user following back')
            ->addOption(
                self::OPTION_USERNAME,
                'u',
                InputOption::VALUE_REQUIRED,
                'Github username',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption(self::OPTION_USERNAME);

        if (!$username) {
            $output->writeln('<error>username is mandatory</error>');
            return 2;
        }

        $followBacks = $this->githubUserFollowbacksService->getUserFollowbacks($username);
        $followBacksArray = $followBacks->toArray();

        $response = [
            'userName' => $username,
            'followBacks' => [
                'count' => count($followBacksArray),
                'userNames' => array_map(fn($user) => $user['username'], $followBacksArray)
            ],
        ];

        echo json_encode($response, JSON_THROW_ON_ERROR);

        return 0;
    }
}
