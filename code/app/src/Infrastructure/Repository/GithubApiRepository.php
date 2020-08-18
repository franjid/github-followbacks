<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Collection\GithubUserCollection;
use App\Domain\Entity\GithubUser;
use App\Domain\Interfaces\Repository\GithubRepositoryInterface;
use App\Infrastructure\Exception\GithubApiErrorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubApiRepository implements GithubRepositoryInterface
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $username
     *
     * @return GithubUserCollection
     * @throws GithubApiErrorException
     */
    public function getUserFollowers(string $username): GithubUserCollection
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf(
                    'https://api.github.com/users/%s/followers',
                    $username
                ),
            );

            $results = $response->toArray();
        } catch (\Throwable $e) {
            throw new GithubApiErrorException($e->getMessage(), $e->getCode());
        }

        if (empty($results)) {
            return new GithubUserCollection();
        }

        $users = [];

        foreach ($results as $user) {
            $users[] = new GithubUser($user['id'], $user['login']);
        }

        return new GithubUserCollection(...$users);
    }
}
