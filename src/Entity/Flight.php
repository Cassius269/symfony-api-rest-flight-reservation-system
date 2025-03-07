<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\Get;
use App\Dto\FlightRequestDto;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\State\FlightStateProcessor;
use App\Repository\FlightRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\State\CustomGetCollectionAvailableFlightsProvider;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ApiResource( // Déclaration de l'entité Flight comme ressource de l'API
    security: "is_granted('ROLE_ADMIN')", // seul un utilisateur au rôle Admin peut avoir accès à toutes les opérations d'une ressource
    operations: [
        new Get(), // récuperer une ressource vol d'avion à l'aide de son ID
        new GetCollection(
            // récuperer l'ensemble des ressources de type vol d'avion disponibles dans le serveur
            paginationEnabled: true, // activer la pagination
            paginationItemsPerPage: 15, // nbre d'items par page
            paginationClientEnabled: true, // donner la possibilité au client de choisir d'activer ou pas la pagination
            paginationClientItemsPerPage: true, // donner la possible au client de choisir le nombre de ressources par page

            // Injection de filtre personnalisé déclaré depuis le fichier "/config/packages/filters.yaml"
            filters: ['flight.search_filter'],
            // Paramètrage optionnel pour transformer les paramètres optionnelles de requêtes de majuscules en minuscule
            parameters: [
                'datedeparture' => new QueryParameter(filter: 'flight.search_filter', property: 'dateDeparture'),
                'datearrival' => new QueryParameter(filter: 'flight.search_filter', property: 'dateArrival'),
                'citydeparture' => new QueryParameter(filter: 'flight.search_filter', property: 'cityDeparture.name'),
                'cityarrival' => new QueryParameter(filter: 'flight.search_filter', property: 'cityArrival.name'),
            ],
        ), // récuperer l'ensemble des ressources de type vol d'avion présent dans le serveur
        new GetCollection(
            // récuperer l'ensemble des ressources de type vol d'avion disponibles dans le serveur
            paginationEnabled: true, // activer la pagination
            paginationItemsPerPage: 15, // nbre d'items par page
            paginationClientEnabled: true, // donner la possibilité au client de choisir d'activer ou pas la pagination
            paginationClientItemsPerPage: true, // donner la possible au client de choisir le nombre de ressources par page
            uriTemplate: '/get-available-flights', // création d'une route personnalisée (endpoint)
            name: 'getAvailableFlights',
            provider: CustomGetCollectionAvailableFlightsProvider::class,
            security: 'is_granted("ROLE_PASSENGER") or is_granted("ROLE_ADMIN")', // seul un utilisateur ayant le rôle Admin ou passager peut regarder l'ensemble des vols disponibles
            filters: ['flight.search_filter'], // injection de filtre personnalisé crée sous forme de service
        ),
        new Post(
            // créer une nouvelle ressource vol d'avion
            processor: FlightStateProcessor::class,
            input: FlightRequestDto::class
        ),
        new Patch(), // modifier une ressource vol d'avion à l'aide de son ID
        new Delete() // supprimer une ressource vol d'avion à l'aide de son ID
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['dateDeparture', 'dateArrival'])]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Une date de départ doit être renseignée")]
    private ?\DateTimeInterface $dateDeparture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Une date d'arrivée doit être renseignée")]
    private ?\DateTimeInterface $dateArrival = null;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Une ville de départ doit être renseignée")]
    private ?City $cityDeparture = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Une ville d'arrivée doit être renseignée")]
    private ?City $cityArrival = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'flight', orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Airplane $airplane = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $reservation->setFlight($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getFlight() === $this) {
                $reservation->setFlight(null);
            }
        }

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
     * Get the value of dateDeparture
     */
    public function getDateDeparture()
    {
        return $this->dateDeparture;
    }

    /**
     * Set the value of dateDeparture
     *
     * @return  self
     */
    public function setDateDeparture($dateDeparture)
    {
        $this->dateDeparture = $dateDeparture;

        return $this;
    }

    /**
     * Get the value of dateArrival
     */
    public function getDateArrival()
    {
        return $this->dateArrival;
    }

    /**
     * Set the value of dateArrival
     *
     * @return  self
     */
    public function setDateArrival($dateArrival)
    {
        $this->dateArrival = $dateArrival;

        return $this;
    }

    /**
     * Get the value of cityDeparture
     */
    public function getCityDeparture(): City
    {
        return $this->cityDeparture;
    }

    /**
     * Set the value of cityDeparture
     *
     * @return  self
     */
    public function setCityDeparture($cityDeparture)
    {
        $this->cityDeparture = $cityDeparture;

        return $this;
    }

    /**
     * Get the value of cityArrival
     */
    public function getCityArrival(): City
    {
        return $this->cityArrival;
    }

    /**
     * Set the value of cityArrival
     *
     * @return  self
     */
    public function setCityArrival($cityArrival)
    {
        $this->cityArrival = $cityArrival;

        return $this;
    }

    public function getAirplane(): ?Airplane
    {
        return $this->airplane;
    }

    public function setAirplane(?Airplane $airplane): static
    {
        $this->airplane = $airplane;

        return $this;
    }
}
