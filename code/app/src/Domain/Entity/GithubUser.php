<?php

namespace App\Domain\Entity;

class GithubUser
{
    public const FIELD_ID = 'id';
    public const FIELD_USERNAME = 'username';

    private int $id;
    private string $username;

    public function __construct(int $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function toArray(): array
    {
        return [
            self::FIELD_ID => $this->getId(),
            self::FIELD_USERNAME => $this->getUsername(),
        ];
    }
}
