<?php

namespace App\Tests;

use App\Tests\Factory\AdminFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

// UsersTest.php
final class UsersTest extends AbstractTest
{
    use ResetDatabase, Factories;  // ← manquait

    public function testAdminResource(): void
    {
        $token = $this->getToken([
            'email' => 'admin@example.com',
            'password' => '$3cr3t',
        ]);

        $this->createClientWithCredentials($token)
            ->request('GET', '/api/me');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginAsUser(): void
    {
        // Créer un user normal
        AdminFactory::createOne([
            'email' => 'user@example.com',
            'password' => '$3cr3t',
            'roles' => ['ROLE_USER'],
        ]);

        // Authentifier l'utilisateur en générant en token si authentification réussie
        $token = $this->getToken([
            'email' => 'user@example.com',
            'password' => '$3cr3t',
        ]);

        $response = $this->createClientWithCredentials($token)
            ->request('POST', '/api/flights', ['json' => []]);

        $this->assertJsonContains(['description' => 'Accès interdit car vous n\'êtes pas admin']);
        $this->assertResponseStatusCodeSame(403);
    }
}
