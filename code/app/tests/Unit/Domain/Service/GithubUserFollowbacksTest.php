<?php

namespace Tests\Unit\Domain\Service;

use App\Domain\Entity\Collection\GithubUserCollection;
use App\Domain\Entity\GithubUser;
use App\Domain\Exception\GithubUserNotFoundException;
use App\Domain\Interfaces\Repository\GithubRepositoryInterface;
use App\Domain\Service\GithubUserFollowbacksService;
use App\Infrastructure\Exception\GithubApiErrorException;
use PHPUnit\Framework\TestCase;

class GithubUserFollowbacksTest extends TestCase
{
    private GithubRepositoryInterface $githubRepositoryMock;
    private GithubUserFollowbacksService $githubUserFollowbacksService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->githubRepositoryMock = $this->createMock(GithubRepositoryInterface::class);
        $this->githubUserFollowbacksService = new GithubUserFollowbacksService($this->githubRepositoryMock);
    }

    public function testUserNotFound(): void
    {
        $this->expectException(GithubUserNotFoundException::class);

        $username = 'username';

        $this->githubRepositoryMock
            ->expects(static::once())
            ->method('getUserFollowers')
            ->with($username)
            ->willThrowException(new GithubApiErrorException('404', 404));

        $this->githubUserFollowbacksService->getUserFollowbacks($username);
    }

    /**
     * @dataProvider followsDataProvider
     *
     * @param array $followersArray
     * @param array $followingArray
     * @param array $expectedFollowbacksArray
     */
    public function testUserFollowbacks(
        array $followersArray,
        array $followingArray,
        array $expectedFollowbacksArray
    ): void
    {
        $username = 'username';
        $followers = new GithubUserCollection(
            ...array_map(
            static function ($user) {
                return new GithubUser($user['id'], $user['username']);
            }
            , $followersArray
        ));
        $following = new GithubUserCollection(
            ...array_map(
            static function ($user) {
                return new GithubUser($user['id'], $user['username']);
            }
            , $followingArray
        ));
        $expectedFollowbacks = new GithubUserCollection(
            ...array_map(
            static function ($user) {
                return new GithubUser($user['id'], $user['username']);
            }
            , $expectedFollowbacksArray
        ));

        $this->githubRepositoryMock
            ->expects(static::once())
            ->method('getUserFollowers')
            ->with($username)
            ->willReturn($followers);

        $this->githubRepositoryMock
            ->expects(static::once())
            ->method('getUserFollowing')
            ->with($username)
            ->willReturn($following);

        $followbacks = $this->githubUserFollowbacksService->getUserFollowbacks($username);

        self::assertEquals($expectedFollowbacks, $followbacks);
    }

    public function followsDataProvider(): array
    {
        return [
            [
                'followers' => [],
                'following' => [],
                'expectedFollowbacks' => []
            ],
            [
                'followers' => [
                    [
                        'id' => 1,
                        'username' => 'username-1'
                    ],
                    [
                        'id' => 2,
                        'username' => 'username-2'
                    ],
                ],
                'following' => [],
                'expectedFollowbacks' => []
            ],
            [
                'followers' => [],
                'following' => [
                    [
                        'id' => 3,
                        'username' => 'username-3'
                    ],
                    [
                        'id' => 4,
                        'username' => 'username-4'
                    ],
                ],
                'expectedFollowbacks' => []
            ],
            [
                'followers' => [
                    [
                        'id' => 1,
                        'username' => 'username-1'
                    ],
                    [
                        'id' => 2,
                        'username' => 'username-2'
                    ],
                    [
                        'id' => 3,
                        'username' => 'username-3'
                    ],
                    [
                        'id' => 4,
                        'username' => 'username-4'
                    ],
                    [
                        'id' => 5,
                        'username' => 'username-5'
                    ],
                ],
                'following' => [
                    [
                        'id' => 9,
                        'username' => 'username-9'
                    ],
                    [
                        'id' => 8,
                        'username' => 'username-8'
                    ],
                    [
                        'id' => 7,
                        'username' => 'username-7'
                    ],
                    [
                        'id' => 6,
                        'username' => 'username-6'
                    ],
                    [
                        'id' => 5,
                        'username' => 'username-5'
                    ],
                    [
                        'id' => 4,
                        'username' => 'username-4'
                    ],
                ],
                'expectedFollowbacks' => [
                    [
                        'id' => 4,
                        'username' => 'username-4'
                    ],
                    [
                        'id' => 5,
                        'username' => 'username-5'
                    ],
                ]
            ],
        ];
    }
}
