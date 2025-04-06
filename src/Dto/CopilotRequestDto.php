<?php

namespace App\Dto;

// Création d'un DTO pour récupérer les données d'un copilote entrées côtés client
class CopilotRequestDto
{
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?string $password = null;
}
