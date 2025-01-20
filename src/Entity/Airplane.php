<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AirplaneRepository;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AirplaneRepository::class)]
#[UniqueEntity(
    fields: ['model'],
    message: 'Un modèle similaire d\'avion est déjà présente dans le serveur'
)]
#[ApiResource(
    operations: [
        new Get(), // obtenir une ressource Avion à l'aide de son ID
        new GetCollection(), // obtenir toutes les ressources de type avion se trouvant dans le serveur
        new Post(), // créer une nouvelle ressource de type Avion à l'aide de son ID
        new Delete(), // supprimer une ressource du serveur à l'aide son ID
        new Patch(), // mettre à jour partiellement une ressource à l'aide de son ID
    ]
)]
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
    #[Assert\Positive(message: 'La capacité de l\'avion doit être un nombre supérieur à zéro')]
    private ?int $capacity = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de création de la donnée doit être renseignée')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

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
}
