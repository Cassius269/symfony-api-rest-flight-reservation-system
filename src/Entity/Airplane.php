<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AirplaneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AirplaneRepository::class)]
#[ApiResource()] // Déclarer l'entité Airplane en tant que ressource avec tous les verbes HTTP autorisés
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

    /**
     * @var Collection<int, Flight>
     */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'airplane')]
    private Collection $flights;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Flight>
     */
    public function getFlights(): Collection
    {
        return $this->flights;
    }

    public function addFlight(Flight $flight): static
    {
        if (!$this->flights->contains($flight)) {
            $this->flights->add($flight);
            $flight->setAirplane($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            // set the owning side to null (unless already changed)
            if ($flight->getAirplane() === $this) {
                $flight->setAirplane(null);
            }
        }

        return $this;
    }
}
