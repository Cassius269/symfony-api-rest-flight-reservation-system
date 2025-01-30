<?php

namespace App\Dto;

// Création d'un DTO pour l'affichage de ressource City
class CityRequestDto
{
    // Etant que le DTO sert de couche de passage de données entre le serveur et le client, pas de grand besoin de getteur et setteur
    public ?int $id = null;
    public ?string $name = null;
    public ?string $country = null;
}
