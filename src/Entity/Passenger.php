<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\State\PassengerStateProvider;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\PassengerRequestDto;
use App\Repository\PassengerRepository;
use App\State\InsertPassengerProcessor;
use App\State\UpdatePassengerProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\State\CustomPassengerGetCollectionStateProvider;
use App\State\PassengerStateProcessor;

#[ORM\Entity(repositoryClass: PassengerRepository::class)]
#[ApiResource(
    // security: "is_granted('ROLE_ADMIN')", // seul un utilisateur au rôle Admin peut avoir accès à toutes les opérations d'une ressource
    securityMessage: 'Desolé, vous n\'avez pas le rôle Admin ou ce ne sont pas vos informations personnelles',
    operations: [
        new GetCollection( // récupérer toutes les ressources passagers
            paginationEnabled: true, // pagination de la data activée par défaut
            paginationItemsPerPage: 10,  // définir le nombre de ressources Passagers à afficher par page, 
            paginationClientEnabled: true, // donner la possibilité au client de choisir l'activation de la pagination
            paginationClientItemsPerPage: true, // donner la possibilité au client de choisir le nombre d'objets ressources par page, 
            provider: CustomPassengerGetCollectionStateProvider::class
        ),
        new Get( // récuperer une ressource passager à l'aide de son ID
            // security: 'is_granted("PASSENGER_VIEW", object)', // syntaxe applicable si endpoint sans provider
            provider: PassengerStateProvider::class, // liaison du provider à l'endpoint de récupération d'une ressource de type passager, 
        ),
        new Post( // envoyer une nouvelle ressource passager au serveur
            processor: PassengerStateProcessor::class, // liaison du processeur à l'endpoint de création de ressource passagers, 
            input: PassengerRequestDto::class
            // denormalizationContext: ['groups' => ['passenger:write']],
            // normalizationContext: ['groups' => ['passenger:read']]

        ),
        new Patch( // modifier partiellement une ressource passager présente dans le serveur à l'aide de son ID,
            security: 'is_granted("PASSENGER_EDIT", object)', // syntaxe applicable si endpoint sans processor
            processor: UpdatePassengerProcessor::class,
            input: PassengerRequestDto::class
        ),
        new Delete( // supprimer une ressource passager présent dans le serveur à l'aide de son ID
            security: 'is_granted("PASSENGER_DELETE", object)', // syntaxe applicable si endpoint sans provider
        )
    ]
)]
class Passenger extends User
{
    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'passenger', cascade: ['persist', 'remove'], orphanRemoval: true)]
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
