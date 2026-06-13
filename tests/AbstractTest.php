<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Factory\AdminFactory;

abstract class AbstractTest extends ApiTestCase
{
    private ?string $token = null;


    protected function setUp(): void
    {
        parent::setUp();
        AdminFactory::createOne([
            'email' => 'admin@example.com',
            'password' => '$3cr3t',
            'roles' => ['ROLE_ADMIN'],
        ]);
    }

    // Envoyer une requête avec le token JWT
    protected function createClientWithCredentials(?string $token = null): Client
    {
        $token ??= $this->getToken();
        return static::createClient([], [
            'auth_bearer' => $token,
        ]);
    }

    // Générer le token JWT d'authentification
    protected function getToken(array $credentials = []): string
    {
        if ($this->token && empty($credentials)) {
            return $this->token;
        }

        $client = static::createClient();

        $client->request('POST', '/api/auth', [
            'json' => $credentials ?: [
                'email' => 'admin@example.com',
                'password' => '$3cr3t',
            ],
        ]);

        $token = $client->getCookieJar()->get('token')->getvalue(); // récupérer le token

        if (empty($credentials)) {
            $this->token = $token;
        }

        return $token;
    }
}
