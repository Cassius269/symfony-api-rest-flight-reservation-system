<?php

namespace App\Service;

use App\Entity\Passenger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}


    public function hashPassword(string $plainTextPassword, Passenger $passenger)
    {
        // Hasher le mot de passe 
        $hashedPassword = $this->passwordHasher->hashPassword(
            $passenger,
            $plainTextPassword
        );

        // Stoker le mot de passe hashé dans le nouvel objet Passenger
        $passenger->setPassword($hashedPassword);
    }
}
