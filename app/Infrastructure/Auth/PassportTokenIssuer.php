<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Ports\OAuthTokenIssuer;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PassportTokenIssuer implements OAuthTokenIssuer
{
    public function issue(string $email, string $password): array
    {
        $request = Request::create(config('app.url') . '/oauth/token', 'POST', [
                'grant_type' => 'password',
                'client_id' => config('services.passport.password_client_id'),
                'client_secret' => config('services.passport.password_client_secret'),
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ]
        );

        $response = app()->handle($request);
        $data = json_decode($response->getContent(), true);

        return $data;
    }
}
