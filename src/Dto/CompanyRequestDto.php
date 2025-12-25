<?php

namespace App\Dto;

class CompanyRequestDto
{
    // Les propriétés de visibilité publique pour récuperer les donnnées du client
    public string $name;
    public ?string $codeIata = null;
}
