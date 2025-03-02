<?php

namespace App\Entity;

use App\Repository\AirplaneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AirplaneRepository::class)]
class Airplane
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'La réference interne de l\'exemplaire de l\'avion est obligatoire')]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'airplanes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AirplaneModel $airplaneModel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAirplaneModel(): ?AirplaneModel
    {
        return $this->airplaneModel;
    }

    public function setAirplaneModel(?AirplaneModel $airplaneModel): static
    {
        $this->airplaneModel = $airplaneModel;

        return $this;
    }
}
