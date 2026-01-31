<?php

namespace App\Service;

use App\Constants\Defaults;
use App\Exception\UnauthorizedException;
use App\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\Request;

class AuthorizationService
{
    public function __construct(
        protected readonly TokenRepository $repository,
    ) {
    }

    public function check(Request $request): void
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader) {
            throw new UnauthorizedException('Missing authorization token', 401);
        }

        $prefix = 'Bearer ';

        if (!str_starts_with($authHeader, $prefix)) {
            throw new UnauthorizedException('Wrong authentication token', 401);
        }

        $tokenString = substr($authHeader, strlen($prefix));
        $hash = hash(Defaults::HASH_ALGO, $tokenString);

        $token = $this->repository->getByHash($hash);

        if (!$token) {
            throw new UnauthorizedException('Wrong authentication token', 401);
        }
    }
}
