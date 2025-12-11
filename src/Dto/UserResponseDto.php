<?php

namespace App\Dto;

class UserResponseDto {
    // Les propriétés publiques à exposer au client
    public int $id;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?string $role = null; // rôle en tant que fonction métier mais pas rôle canonique dans l'application
}