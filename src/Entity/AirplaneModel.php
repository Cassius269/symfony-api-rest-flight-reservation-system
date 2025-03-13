<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Dto\AirplaneModelRequestDto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\AirplaneModelStateProvider;
use App\State\AirplaneModelStateProcessor;
use App\Repository\AirplaneModelRepository;
use App\State\UpdateAirplaneModelProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\State\CustomAirplaneGetCollectionStateProvider;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AirplaneModelRepository::class)]
#[UniqueEntity(
    fields: ['model'],
    message: 'Un modèle similaire d\'avion est déjà présente dans le serveur'
)]
#[ApiResource(
    // Exposition des champs en phase de sérialisation
    // normalizationContext: ['groups' => ['airplane:read']], // convertir un objet Airplane au format json, utile en lecture
    security: "is_granted('ROLE_ADMIN')", // par défaut seul un utilisateur au rôle Admin peut avoir accès à toutes les opérations d'une ressource de type modèle d'avion
    operations: [
        new Get( // obtenir une ressource Avion à l'aide de son ID
            provider: AirplaneModelStateProvider::class
        ),
        new GetCollection( // obtenir toutes les ressources de type avion se trouvant dans le serveur
            paginationEnabled: true, // activer la pagination des ressources Avions
            paginationItemsPerPage: 10, // 10 ressources avions affichées par page
            provider: CustomAirplaneGetCollectionStateProvider::class
        ),
        new Post(
            // créer une nouvelle ressource de type Avion à l'aide de son ID
            // security: "is_granted('ROLE_ADMIN')", // seul un utilisateur avec le rôle d'administrateur peut enregistrer une nouvelle ressource de type avion
            processor: AirplaneModelStateProcessor::class,
            input: AirplaneModelRequestDto::class, // DTO d'entrée dédié
        ),
        new Delete(), // supprimer une ressource du serveur à l'aide son ID
        new Patch( // mettre à jour partiellement une ressource à l'aide de son ID
            processor: UpdateAirplaneModelProcessor::class,
            input: AirplaneModelRequestDto::class,
            // convertir une donnée JSON en objet, utile en écriture
            // normalizationContext: ['groups' => ['airplane:write']],
        ),
    ]
)]
class AirplaneModel
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
    // #[Groups(['airplane:write'])]
    private ?string $model = null; // le modele officiel donné par le fabricant de l'avion

    #[ORM\Column]
    #[Assert\Positive(message: 'La capacité de l\'avion doit être un nombre supérieur à zéro')]
    // #[Groups(['airplane:write'])]
    private ?int $capacity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de création de la donnée doit être renseignée')]
    // #[Groups(['airplane:read', 'airplane:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    // #[Groups(['airplane:read', 'airplane:write'])]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Airplane>
     */
    #[ORM\OneToMany(targetEntity: Airplane::class, mappedBy: 'airplaneModel', orphanRemoval: true)]
    private Collection $airplanes;

    public function __construct()
    {
        $this->airplanes = new ArrayCollection();
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
            $airplane->setAirplaneModel($this);
        }

        return $this;
    }

    public function removeAirplane(Airplane $airplane): static
    {
        if ($this->airplanes->removeElement($airplane)) {
            // set the owning side to null (unless already changed)
            if ($airplane->getAirplaneModel() === $this) {
                $airplane->setAirplaneModel(null);
            }
        }

        return $this;
    }
}
