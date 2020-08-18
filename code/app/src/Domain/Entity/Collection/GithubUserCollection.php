<?php

namespace App\Domain\Entity\Collection;

use App\Domain\Entity\GithubUser;

class GithubUserCollection
{
    /** @var GithubUser[] $githubUsers */
    private array $githubUsers;

    public function __construct(GithubUser ...$githubUsers)
    {
        $this->githubUsers = $githubUsers;
    }

    public function getItems(): array
    {
        return $this->githubUsers;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->githubUsers as $githubUser) {
            $result[] = $githubUser->toArray();
        }

        return $result;
    }
}
