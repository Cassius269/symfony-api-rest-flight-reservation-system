<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}


    public function hashPassword(string $plainTextPassword, User $user)
    {
        // Hasher le mot de passe 
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainTextPassword
        );

        // Stoker le mot de passe hashé dans le nouvel objet Passenger
        $user->setPassword($hashedPassword);
    }
}
