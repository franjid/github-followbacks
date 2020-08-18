<?php

namespace App\Domain\Interfaces\Repository;

use App\Domain\Entity\Collection\GithubUserCollection;

interface GithubRepositoryInterface
{
    public function getUserFollowers(string $username): GithubUserCollection;
}
