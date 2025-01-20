<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AirplaneRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AirplaneRepository::class)]
#[ApiResource]
class Airplane
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    #[Assert\NotBlank(message: 'Le modèle de l\'avion doit être renseigné')]
    #[Assert\Length(
        max: 12,
        maxMessage: 'Le modèle de l\'avion ne doit pas dépasser 12 caractères'
    )]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'La capacité doit être un nombre supérieur à zéro')]
    private ?int $capacity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de création de la donnée doit être renseignée')]
    #[Assert\DateTime('L\'information doit être au format date Y-m-d H:i:s')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
