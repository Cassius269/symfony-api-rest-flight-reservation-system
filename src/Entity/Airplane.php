<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use App\Dto\AirplaneRequestDto;
use ApiPlatform\Metadata\Delete;
use App\Dto\AirplaneResponseDto;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\AirplaneStateProcessor;
use App\Repository\AirplaneRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AirplaneRepository::class)]
#[UniqueEntity(
    fields: ['model'],
    message: 'Un modèle similaire d\'avion est déjà présente dans le serveur'
)]
#[ApiResource(
    // Exposition des champs en phase de sérialisation
    // normalizationContext: ['groups' => ['airplane:read']], // convertir un objet Airplane au format json, utile en lecture
    operations: [
        new Get(), // obtenir une ressource Avion à l'aide de son ID
        new GetCollection( // obtenir toutes les ressources de type avion se trouvant dans le serveur
            paginationEnabled: true, // activer la pagination des ressources Avions
            paginationItemsPerPage: 10 // 10 ressources avions affichées par page
        ),
        new Post(
            // créer une nouvelle ressource de type Avion à l'aide de son ID
            processor: AirplaneStateProcessor::class,
            input: AirplaneRequestDto::class, // DTO d'entrée dédié
        ),
        new Delete(), // supprimer une ressource du serveur à l'aide son ID
        new Patch( // convertir une donnée JSON en objet, utile en écriture
            normalizationContext: ['groups' => ['airplane:write']],
        ), // mettre à jour partiellement une ressource à l'aide de son ID
    ]
)]
class Airplane
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // #[Groups(['airplane:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    #[Assert\NotBlank(message: 'Le modèle de l\'avion doit être renseigné')]
    #[Assert\Length(
        max: 12,
        maxMessage: 'Le modèle de l\'avion ne doit pas dépasser 12 caractères'
    )]
    // #[Groups(['airplane:read', 'airplane:write'])]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'La capacité de l\'avion doit être un nombre supérieur à zéro')]
    // #[Groups(['airplane:read', 'airplane:write'])]
    private ?int $capacity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de création de la donnée doit être renseignée')]
    // #[Groups(['airplane:read', 'airplane:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    // #[Groups(['airplane:read', 'airplane:write'])]
    private ?\DateTimeInterface $updatedAt = null;

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
