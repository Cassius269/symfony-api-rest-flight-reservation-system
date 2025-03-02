<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Dto\PassengerRequestDto;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PassengerRepository;
use App\State\InsertPassengerProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PassengerRepository::class)]
#[ApiResource(
    security: "is_granted('ROLE_ADMIN')", // seul un utilisateur au rôle Admin peut avoir accès à toutes les opérations d'une ressource
    normalizationContext: ['groups' => ['passenger.read']],
    // denormalizationContext: ['groups' => ['passenger.write']],
    operations: [
        new GetCollection(), // récupérer toutes les ressources passagers
        new Get(), // récuperer une ressource passager à l'aide de son ID
        new Post( // envoyer une nouvelle ressource passager au serveur
            processor: InsertPassengerProcessor::class, // liaison du processeur à la route de création de ressource passagers, 
            // input: PassengerRequestDto::class

        ),
        new Patch(), // modifier une ressource passager présente dans le serveur à l'aide de son ID,
        new Delete() // supprimer une ressource passager présent dans le serveur à l'aide de son ID
    ]
)]
class Passenger extends User
{
    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'passenger')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setPassenger($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPassenger() === $this) {
                $reservation->setPassenger(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {

        return $this->getFirstname() . ' ' . $this->getLastname() . ' avec l\'email ' . $this->getEmail();
    }
}
