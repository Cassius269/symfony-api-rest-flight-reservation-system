<?php

namespace App\Tests;

final class UsersTest extends AbstractTest
{
    public function testAdminResource()
    {
        $token = $this->getToken([
            'email' => 'admin@example.com',
            'password' => '$3cr3t',
        ]);

        $this->createClientWithCredentials($token)
            ->request('GET', '/users');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginAsUser()
    {
        $token = $this->getToken([
            'email' => 'user@example.com',
            'password' => '$3cr3t',
        ]);

        $response = $this->createClientWithCredentials($token)->request('GET', '/users');
        $this->assertJsonContains(['description' => 'Access Denied.']);
        $this->assertResponseStatusCodeSame(403);
    }
}
