<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\StatusRequestDto;
use App\Dto\StatusResponseDto;
use App\Entity\Trait\DateTrait;
use App\Repository\StatusRepository;
use App\State\InsertStatusProcessor;
use App\State\StatusStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    securityMessage: 'Vous n\'êtes pas Admin',
    operations: [
        new Get(
            provider: StatusStateProvider::class // traitement personnalisé pour récupérer une ressource à l'aide de son ID
        ),
        new GetCollection(),
        new Post(
            input: StatusRequestDto::class,
            processor: InsertStatusProcessor::class
        ),
        new Patch(),
        new Delete()
    ]
)]
#[UniqueEntity(
    fields: ['name'],
    message: 'Le nom d\'un status doit être unique',
    errorPath: 'name'
)]
class Status
{
    // Utiliser le trait des dates de création et de mise à jour
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: 'Le nom du status est obligatoire')]
    #[Assert\Length(
        min: 4,
        max: 15,
        minMessage: 'Le nom du status doit avoir au moins 4 caractères',
        maxMessage: 'Le nom du status doit avoir moins de 15 caractères'

    )]
    private ?string $name = null;

    /**
     * @var Collection<int, Flight>
     */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'status')]
    private Collection $flights;

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
            $flight->setStatus($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            // set the owning side to null (unless already changed)
            if ($flight->getStatus() === $this) {
                $flight->setStatus(null);
            }
        }

        return $this;
    }
}
