<?php

namespace App\Dto;

class PassengerResponseDto
{
    // Rendre publiques les données que l'on souhaite traiter
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?string $password = null;
}
