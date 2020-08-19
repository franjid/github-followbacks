<?php

namespace App\Domain\Service;

use App\Domain\Entity\Collection\GithubUserCollection;
use App\Domain\Entity\GithubUser;
use App\Domain\Exception\GithubErrorException;
use App\Domain\Exception\GithubUserNotFoundException;
use App\Domain\Interfaces\Repository\GithubRepositoryInterface;
use App\Infrastructure\Exception\GithubApiErrorException;

class GithubUserFollowbacksService
{
    private GithubRepositoryInterface $githubRepository;

    public function __construct(GithubRepositoryInterface $githubRepository)
    {
        $this->githubRepository = $githubRepository;
    }

    public function getUserFollowbacks(string $username): GithubUserCollection
    {
        $followBacks = new GithubUserCollection();

        try {
            $followers = $this->githubRepository->getUserFollowers($username)->toArray();
            $following = $this->githubRepository->getUserFollowing($username)->toArray();

            $serializedFollowers = array_map('serialize', $followers);
            $serializedFollowing = array_map('serialize', $following);

            $followBacksArray = array_map('unserialize', array_intersect($serializedFollowers, $serializedFollowing));
            $followBacks = new GithubUserCollection(
                ...array_map(
                fn($user) => new GithubUser($user['id'], $user['username']),
                $followBacksArray
            ));
        } catch (GithubApiErrorException $e) {
            if ($e->getCode() === 404) {
                throw new GithubUserNotFoundException('Username ' . $username . ' not found in Github');
            }

            throw new GithubErrorException($e->getMessage(), $e->getCode());
        }

        return $followBacks;
    }
}
