<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\CopilotRequestDto;
use App\Repository\CopilotRepository;
use App\State\CopilotStateProvider;
use App\State\CustomCopilotsGetCollectionProvider;
use App\State\InsertCopilotProcessor;
use App\State\UpdateCopilotProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CopilotRepository::class)]
#[ApiResource( // Déclarer l'entité Copilot en tant que ressource de l'API
    operations: [
        new Get( // récuperer un copilote à l'aide son Id
            provider: CopilotStateProvider::class, // traitement personnalisé de récupération d'un copilote
            security: "is_granted('ROLE_ADMIN')", // vérifier la permission de l'utilisateur
            securityMessage: 'Vous devez avoir un rôle Admin pour accéder à cet endpoint'
        ),
        new GetCollection(
            provider: CustomCopilotsGetCollectionProvider::class, // traitement personnalisé de récupération de tous les copilotes
            security: "is_granted('ROLE_ADMIN')", // vérifier la permission de l'utilisateur
            securityMessage: 'Vous devez avoir un rôle Admin pour accéder à cet endpoint'
        ),
        new Post( // enregistrer une nouvelle ressource utilisateur de type Copilote
            input: CopilotRequestDto::class, // DTO de récupération des données fournies par le client
            processor: InsertCopilotProcessor::class, // traitement personnalisé de l'ajout d'un copilote,
            security: "is_granted('COPILOT_CREATE, object)", // vérifier la permission de l'utilisateur sur la création de nouvel
            securityMessage: 'Vous devez avoir un rôle Admin pour accéder à cet endpoint'
        ),
        new Patch( // mettre à jour un copilote à l'aide de son ID
            input: CopilotRequestDto::class,
            processor: UpdateCopilotProcessor::class, // traitement personnalisé de la mise à jour d'un copilote à l'aide de son ID
            security: "is_granted('COPILOT_EDIT', object)",
            securityMessage: 'Vous \'êtes pas autorisé à modifier la ressource'
        ),
        new Delete() // supprimer un copilote à l'aide de son ID
    ]

)]
class Copilot extends User
{
    /**
     * @var Collection<int, Flight>
     */
    #[ORM\ManyToMany(targetEntity: Flight::class, mappedBy: 'copilots')]
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
            $flight->addCopilot($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            $flight->removeCopilot($this);
        }

        return $this;
    }
}
