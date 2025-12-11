<?php

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Repository\AirportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AirportRepository::class)]
class Airport
{
    // Importer le trait des dates de création et mise à jour
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank(message: 'Le nom de l\'aéroport est obligatoire')]
    #[Assert\Length(
        min: 10, 
        max: 40,
        minMessage: 'Le nom de l\'aéroport doit être composé de plus de 10 caractères minimum',
        maxMessage: 'Le nom de l\'aéroport doit avoir moins de 40 caractères'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank(message: "Le code IATA est obligatoire")]
    #[Assert\Length(
        exactly: 3, // le nombre exact de caractères à entrer
        exactMessage: 'Le code IATA est composé de 3 caractères',
    )]
    private ?string $iataCode = null;

    #[ORM\ManyToOne(inversedBy: 'airports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'La ville où se trouve l\'aéroport est obligatoire')]
    private ?City $city = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIataCode(): ?string
    {
        return $this->iataCode;
    }

    public function setIataCode(string $iataCode): static
    {
        $this->iataCode = $iataCode;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }
}
