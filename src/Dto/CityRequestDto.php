<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

// Création d'un DTO pour le recueille des données de création d'une nouvelle ville
class CityRequestDto
{
    // Etant donnée que le DTO sert de couche de passage de données entre le serveur et le client, pas de grand besoin de getteur et setteur
    public ?int $id = null;

    #[Assert\NotBlank(message: "Le nom de la ville est obligatoire")]
    public ?string $name = null;

    #[Assert\NotBlank(message: "Le nom du pays est obligatoire")]
    public ?string $country = null;
}
