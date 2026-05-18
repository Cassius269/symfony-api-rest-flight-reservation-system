<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Dto\CompanyRequestDto;
use App\Entity\Trait\DateTrait;
use App\Repository\CompanyRepository;
use App\State\CustomCompaniesGetAirplanesCollectionStateProvider;
use App\State\CustomCompaniesGetCollectionStateProvider;
use App\State\InsertCompanyStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[UniqueEntity(
    fields: ['iataCode'],
    message: 'Ce code IATA est déjà utilisé.',
)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            provider: CustomCompaniesGetCollectionStateProvider::class,
            // Filtres personnalisés
            parameters: [
                'name' => new QueryParameter(
                    property: 'name',
                    filter: new ExactFilter()
                )
            ]
        ),
        new Post( // Créer une nouvelle compagnie
            input: CompanyRequestDto::class,
            processor: InsertCompanyStateProcessor::class // traitement personnalisé de l'ajout de nouvelle compagnie
        ),
        new Delete(), // supprimer une ressource Company à l'aide de son ID
        // new Patch() // modifier une ressource Company à l'aide de son ID
    ]
)]
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
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'company')]
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

    /**
     * @var Collection<int, CompanyCaptain>
     */
    #[ORM\OneToMany(targetEntity: CompanyCaptain::class, mappedBy: 'company')]
    private Collection $companyCaptains;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->airplanes = new ArrayCollection();
        $this->companyCaptains = new ArrayCollection();
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

    /**
     * @return Collection<int, CompanyCaptain>
     */
    public function getCompanyCaptains(): Collection
    {
        return $this->companyCaptains;
    }

    public function addCompanyCaptain(CompanyCaptain $companyCaptain): static
    {
        if (!$this->companyCaptains->contains($companyCaptain)) {
            $this->companyCaptains->add($companyCaptain);
            $companyCaptain->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyCaptain(CompanyCaptain $companyCaptain): static
    {
        if ($this->companyCaptains->removeElement($companyCaptain)) {
            // set the owning side to null (unless already changed)
            if ($companyCaptain->getCompany() === $this) {
                $companyCaptain->setCompany(null);
            }
        }

        return $this;
    }
}
