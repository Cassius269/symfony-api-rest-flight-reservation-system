<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Dto\CaptainRequestDto;
use ApiPlatform\Metadata\Patch;
use App\Dto\CaptainResponseDto;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\State\CaptainStateProvider;
use App\State\CaptainStateProcessor;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CaptainRepository;
use ApiPlatform\Metadata\GetCollection;
use App\State\CustomCaptainsGetCollectionStateProvider;
use App\State\UpdateCaptainStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CaptainRepository::class)]
#[ApiResource( // Déclarer Commandant de bord en tant que ressource de l'API
    operations: [
        new Get( // rendre accessible une ressource grâce à son ID 
            provider: CaptainStateProvider::class, // traitement personnalisée de la réponse de l'endpoint
            output: CaptainResponseDto::class,
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
            securityMessage: 'Vous n\'êtes ni Admin ni propriétaires des données personnelles'
        ),
        new GetCollection( // rendre accessible l'ensemble des ressources 
            provider: CustomCaptainsGetCollectionStateProvider::class // traitement personnalisé de l'endpoint de récupération de tous les commandants de bord
        ),
        new Post( // créer une nouvelle ressource 
            processor: CaptainStateProcessor::class,
            input: CaptainRequestDto::class,
        ),
        new Patch( // mettre à jour une ressource en particulier de façon partielle 
            processor: UpdateCaptainStateProcessor::class // traitement personnalisé de la mise d'une ressource de type commandant de bord
        ),
        new Delete( // supprimer une ressource Commandant de bord 
            security: "is_granted('ROLE_ADMIN') or object.owner == user",
            securityMessage: 'Vous n\'êtes ni Admin ni propriétaires des données personnelles'
        )
    ]
)]
class Captain extends User
{
    /**
     * @var Collection<int, Flight>
     */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'captain')]
    private Collection $flights;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
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
            $flight->setCaptain($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            // set the owning side to null (unless already changed)
            if ($flight->getCaptain() === $this) {
                $flight->setCaptain(null);
            }
        }

        return $this;
    }
}
