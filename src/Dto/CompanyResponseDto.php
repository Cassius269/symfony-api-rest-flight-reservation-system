<?php

namespace App\Dto;

class CompanyResponseDto {
    // Les propriétés de visibilité publique à exposer au client
    public int $id;
    public string $name;
    public ?string $codeIata = null;
}