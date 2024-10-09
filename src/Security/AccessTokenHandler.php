<?php

declare(strict_types=1);

namespace App\Security;

use App\Token\TokenStorage;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private TokenStorage $tokenStorage) 
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        if (null === $tokenData = $this->tokenStorage->get($accessToken)) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($tokenData['user_id']);
    }
}