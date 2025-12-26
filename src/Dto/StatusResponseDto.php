<?php

namespace App\Dto;

class StatusResponseDto
{
    public ?int $id = null;
    public ?string $name = null;
    public ?\DateTimeImmutable $createdAt = null;
    public ?\DateTime $updatedAt = null;
}
