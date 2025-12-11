<?php

namespace App\Dto;

class UserRequestDto {
    // Les propriétés publiques à exposer au client
    public int $id;
    public string $firstname;
    public string $lastname;
    public ?string $role = null; // rôle en tant que fonction métier mais pas rôle canonique dans l'application
}