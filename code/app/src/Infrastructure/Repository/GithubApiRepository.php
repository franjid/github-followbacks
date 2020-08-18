<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Collection\GithubUserCollection;
use App\Domain\Entity\GithubUser;
use App\Domain\Interfaces\Repository\GithubRepositoryInterface;
use App\Infrastructure\Exception\GithubApiErrorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubApiRepository implements GithubRepositoryInterface
{
    private const API_URL = 'https://api.github.com';

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getUserFollowers(string $username): GithubUserCollection
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf(self::API_URL . '/users/%s/followers', $username),
            );

            return $this->populateUsers($response->toArray());
        } catch (\Throwable $e) {
            throw new GithubApiErrorException($e->getMessage(), $e->getCode());
        }
    }

    public function getUserFollowing(string $username): GithubUserCollection
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf(self::API_URL . '/users/%s/following', $username),
            );

            return $this->populateUsers($response->toArray());
        } catch (\Throwable $e) {
            throw new GithubApiErrorException($e->getMessage(), $e->getCode());
        }
    }

    private function populateUsers(array $githubUsersData): GithubUserCollection
    {
        if (empty($githubUsersData)) {
            return new GithubUserCollection();
        }

        $users = [];

        foreach ($githubUsersData as $user) {
            $users[] = new GithubUser($user['id'], $user['login']);
        }

        return new GithubUserCollection(...$users);
    }
}
