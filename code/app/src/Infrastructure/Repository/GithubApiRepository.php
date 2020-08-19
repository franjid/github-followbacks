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
    private const NUM_ELEMENTS_PER_PAGE = 100;

    private HttpClientInterface $githubClient;
    private array $headers = [];

    public function __construct(
        HttpClientInterface $githubClient,
        string $token
    )
    {
        $this->githubClient = $githubClient;

        if ($token !== '') {
            $this->headers = [
                'headers' => [
                    'Authorization' => 'token ' . $token,
                ],
            ];
        }
    }

    public function getUserFollowers(string $username): GithubUserCollection
    {
        return $this->getUserFollows($username, 'followers');
    }

    public function getUserFollowing(string $username): GithubUserCollection
    {
        return $this->getUserFollows($username, 'following');
    }

    private function getUserFollows(string $username, string $followType): GithubUserCollection
    {
        if ($followType !== 'followers' && $followType !== 'following') {
            throw new GithubApiErrorException('Follow type not valid');
        }

        try {
            $results = [];
            $page = 1;

            do {
                $requestUrl = sprintf(
                    self::API_URL . '/users/%s/' . $followType . '?per_page=%d&page=%d',
                    $username,
                    self::NUM_ELEMENTS_PER_PAGE,
                    $page
                );

                $response = $this->githubClient->request('GET', $requestUrl, $this->headers);

                $data = $response->toArray();
                $results[] = $data;
                $page++;
            } while (!empty($data));

            return $this->populateUsers(array_merge(...$results));
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
