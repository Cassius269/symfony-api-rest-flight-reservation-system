<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

// Création d'un DTO pour récupérer les données d'un passager entrées côtés client
class PassengerRequestDto
{
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    public ?string $firstname = null;

    #[Assert\NotBlank(message: 'Le nom de famille est obligatoire')]
    public ?string $lastname = null;

    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    public ?string $email = null;
}
