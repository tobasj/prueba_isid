<?php

namespace App\Domain\Auth\Ports;

interface OAuthTokenIssuer
{
    public function issue(string $email, string $password): array;
}
