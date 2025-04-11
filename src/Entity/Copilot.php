<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CopilotRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CopilotRepository::class)]
#[ApiResource( // Déclarer l'entité Copilot en tant que ressource de l'API
    operations: [
        new Get(), // rendre accessible une ressource grâce à son ID 
        new GetCollection(), // rendre accessible l'ensemble des ressources de type Copilote
        new Post(), // créer une nouvelle resource Copilote
        new Delete() // supprimer une ressource Copilote grâce à son ID
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
