<?php

namespace App\Dto;

// Création d'un DTO pour récupérer les données d'un passager entrées côtés client
class PassengerRequestDto
{
    public string $firstname;
    public string $lastname;
    public string $email;
    public ?string $password = null;
}
