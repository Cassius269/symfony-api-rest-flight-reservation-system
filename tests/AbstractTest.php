<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Factory\AdminFactory;

abstract class AbstractTest extends ApiTestCase
{
    private ?string $token = null;

    protected function createClientWithCredentials(?string $token = null): Client
    {
        $token ??= $this->getToken();

        return static::createClient([], [
            'extra_headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    protected function getToken(array $body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $email = $body['email'] ?? 'admin@example.com';
        $password = $body['password'] ?? '$3cr3t';

        if ($email === 'admin@example.com') {
            AdminFactory::createOne([
                'email' => $email,
                'password' => $password,
                'roles' => ['ROLE_ADMIN'],
            ]);
        }

        return $this->token = $this->createClient()->request('POST', '/api/auth', ['json' => [
            'email' => $email,
            'password' => $password,
        ]])->toArray()['token'];
    }
}
