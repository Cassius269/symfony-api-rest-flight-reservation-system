<?php

namespace App\Dto;

// Création d'un DTO pour récupérer les données d'un Commandant de bord entrées côté client
class CaptainRequestDto
{
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?string $password = null;
}
