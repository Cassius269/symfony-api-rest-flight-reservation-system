<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CaptainRepository;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\CaptainResponseDto;
use App\State\CaptainStateProvider;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CaptainRepository::class)]
#[ApiResource( // Déclarer Commandant de bord en tant que ressource de l'API
    operations: [
        new Get( // rendre accessible une ressource grâce à son ID 
            provider: CaptainStateProvider::class,
            output: CaptainResponseDto::class
        ),
        new GetCollection(), // rendre accessible l'ensemble des ressources 
        new Post(), // créer une nouvelle ressource 
        new Patch(), // mettre à jour une ressource en particulier de façon partielle 
        new Delete() // supprimer une ressource Commandant de bord 
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
