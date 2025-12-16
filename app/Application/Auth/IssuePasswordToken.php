<?php

namespace App\Application\Auth;

use App\Domain\Auth\Ports\OAuthTokenIssuer;

class IssuePasswordToken
{
    public function __construct(private OAuthTokenIssuer $issuer) {

    }

    public function forCredentials(string $email, string $password): array
    {
        return $this->issuer->issue($email, $password);
    }
}
