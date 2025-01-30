<?php

namespace App\Entity;

use App\Dto\CityResponseDto;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\State\CityStateProcessor;
use App\Repository\CityRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\CityRequestDto;
use App\State\CityStateProvider;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ApiResource(
    operations: [
        new Post( // enregistrer une nouvelle ressource City sur le serveur 
            processor: CityStateProcessor::class, // liaison du processor (traitement) avec l'entité
            input: CityResponseDto::class // utilisation d'un Dto plutôt que l'entité pour séparer les responsabilités
        ),
        new Get( // récuperer une ressource City à l'aide de son ID
            provider: CityStateProvider::class,
            output: CityRequestDto::class
        ),
        new GetCollection(), // récuperer l'ensemble des ressources City présentes dans le serveur
        new Delete(), // supprimer une ressource City à l'aide de son ID
        new Patch() // modifier une ressource City à l'aide de son ID
    ]
)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 4,
        minMessage: 'Le nom d\'une ville est trop court'
    )]
    #[Assert\NotBlank(message: 'Le nom d\'une ville est obligatoire')]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'cities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Country $country = null;

    /**
     * @var Collection<int, Flight>
     */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'cityDeparture')]
    private Collection $flights;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(message: 'La date de création de la donnée d\'une ville est obligatoire')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

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
            $flight->setCityDeparture($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            // set the owning side to null (unless already changed)
            if ($flight->getCityDeparture() === $this) {
                $flight->setCityDeparture(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
