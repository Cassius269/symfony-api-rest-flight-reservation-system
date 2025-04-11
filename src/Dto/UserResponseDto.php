<?php

namespace App\Dto;

use DateTime;
use DateTimeImmutable;

class UserResponseDto
{
    public ?int $id = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?array $roles = null;
    public ?string $email = null;
    public ?DateTimeImmutable $createdAt = null;
    public ?DateTime $updtatedAt = null;
}
