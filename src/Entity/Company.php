<?php

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    // Importer le trait des dates de création et de mise à jour
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 35)]
    #[Assert\NotBlank(message: 'Le nom de la compagnie est obligatoire')]
    #[Assert\Length(
        min: 4, 
        minMessage: 'Le nom de la compagnie doit avoir au moins 4 caractères',
        max: 35,
        maxMessage: 'Le nom de la compagnie ne doit pas dépasser 35 caractères'
    )]
    private ?string $name = null;

    /**
     * @var Collection<int, Flight>
     */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'Company')]
    private Collection $flights;

    /**
     * @var Collection<int, CompanyAgent>
     */
    #[ORM\OneToMany(targetEntity: CompanyAgent::class, mappedBy: 'company')]
    private Collection $agents;

    #[ORM\Column(length: 2)]
    private ?string $iataCode = null;

    /**
     * @var Collection<int, Airplane>
     */
    #[ORM\OneToMany(targetEntity: Airplane::class, mappedBy: 'company')]
    private Collection $airplanes;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->airplanes = new ArrayCollection();
    }

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
            $flight->setCompany($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            // set the owning side to null (unless already changed)
            if ($flight->getCompany() === $this) {
                $flight->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompanyAgent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(CompanyAgent $agent): static
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
            $agent->setCompany($this);
        }

        return $this;
    }

    public function removeAgent(CompanyAgent $agent): static
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getCompany() === $this) {
                $agent->setCompany(null);
            }
        }

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

    /**
     * @return Collection<int, Airplane>
     */
    public function getAirplanes(): Collection
    {
        return $this->airplanes;
    }

    public function addAirplane(Airplane $airplane): static
    {
        if (!$this->airplanes->contains($airplane)) {
            $this->airplanes->add($airplane);
            $airplane->setCompany($this);
        }

        return $this;
    }

    public function removeAirplane(Airplane $airplane): static
    {
        if ($this->airplanes->removeElement($airplane)) {
            // set the owning side to null (unless already changed)
            if ($airplane->getCompany() === $this) {
                $airplane->setCompany(null);
            }
        }

        return $this;
    }
}
