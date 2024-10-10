<?php

declare(strict_types=1);

namespace App\Token;

class TokenStorage {
    public const USER_ID = 'user_id';

    public function __construct(private \Redis $redis) {}

    public function set(string $token, string $userId): void
    {
        $this->redis->set($this->buildKey($token), \igbinary_serialize([self::USER_ID => $userId]), ['nx', 'ex' => 3600]);
    }

    public function get(string $token): null|array
    {
        if (false === $serializedData = $this->redis->get($this->buildKey($token))) {
            return null;
        }

        return \igbinary_unserialize($serializedData);
    }

    private function buildKey(string $token): string
    {
        return sprintf('token:%s', $token);
    }
}