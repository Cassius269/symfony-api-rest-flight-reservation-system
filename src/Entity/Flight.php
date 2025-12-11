<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use App\Entity\Trait\DateTrait;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FlightRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\FlightRequestDto;
use App\Dto\FlightRequesteDto;
use App\State\CustomFlightsGetCollection;
use App\State\InsertFlightStateProcessor;
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
            // récuperer l'ensemble des ressources de type vol d'avion dans le serveur
            provider: CustomFlightsGetCollection::class, // traitement personnalisé de récupération de tous les vols présents dans le serveur
            paginationEnabled: true, // activer la pagination
            paginationItemsPerPage: 15, // nbre d'items par page
            paginationClientEnabled: true, // donner la possibilité au client de choisir d'activer ou pas la pagination
            paginationClientItemsPerPage: true, // donner la possible au client de choisir le nombre de ressources par page
            security: 'is_granted("PUBLIC_ACCESS")', // les utilisateurs non connectés peuvent avoir accès à l'ensemble des vols disponibles
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
            security: 'is_granted("PUBLIC_ACCESS")', // les utilisateurs non connectés peuvent avoir accès à l'ensemble des vols disponibles
        ),
        new Post(
            // créer une nouvelle ressource vol d'avion
            processor: InsertFlightStateProcessor::class,
            input: FlightRequestDto::class,
            securityMessage: 'Vous n\'êtes pas Admin'
        ),
        new Patch(), // modifier une ressource vol d'avion à l'aide de son ID
        new Delete() // supprimer une ressource vol d'avion à l'aide de son ID
    ]
)]
class Flight
{
    use DateTrait; // intégrer le trait des dates de créations et de mise à jour

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

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'flight', orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'L\'avion utilisé pour le vol doit être renseigné')]
    private ?Airplane $airplane = null;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(name: 'captain_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotBlank(message: 'Le capitaine du vol doit être renseigné')]
    private ?Captain $captain = null;

    /**
     * @var Collection<int, Copilot>
     */
    #[ORM\ManyToMany(targetEntity: Copilot::class, inversedBy: 'flights')]
    private Collection $copilots;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Il est obligatoire de renseigner si le vol est direct ou non")]
    private ?bool $isDirect = null;

    #[ORM\Column]
    private ?bool $isCanceled = null;

    #[ORM\Column]
    private ?bool $isLate = null;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $Company = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "L'aéroport de départ est obligatoire")]
    private ?Airport $airportDeparture = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "L'aéroport d'arrivée est obligatoire")]
    private ?Airport $airportArrival = null;

    /**
     * @var Collection<int, Stop>
     */
    #[ORM\OneToMany(targetEntity: Stop::class, mappedBy: 'flight')]
    private Collection $stops;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->copilots = new ArrayCollection();
        $this->stops = new ArrayCollection();
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


    public function getAirplane(): ?Airplane
    {
        return $this->airplane;
    }

    public function setAirplane(?Airplane $airplane): static
    {
        $this->airplane = $airplane;

        return $this;
    }

    public function getCaptain(): ?Captain
    {
        return $this->captain;
    }

    public function setCaptain(?Captain $captain): static
    {
        $this->captain = $captain;

        return $this;
    }

    /**
     * @return Collection<int, Copilot>
     */
    public function getCopilots(): Collection
    {
        return $this->copilots;
    }

    public function addCopilot(Copilot $copilot): static
    {
        if (!$this->copilots->contains($copilot)) {
            $this->copilots->add($copilot);
        }

        return $this;
    }

    public function removeCopilot(Copilot $copilot): static
    {
        $this->copilots->removeElement($copilot);

        return $this;
    }

    public function isDirect(): ?bool
    {
        return $this->isDirect;
    }

    public function setIsDirect(bool $isDirect): static
    {
        $this->isDirect = $isDirect;

        return $this;
    }

    public function isCanceled(): ?bool
    {
        return $this->isCanceled;
    }

    public function setIsCanceled(bool $isCanceled): static
    {
        $this->isCanceled = $isCanceled;

        return $this;
    }

    public function isLate(): ?bool
    {
        return $this->isLate;
    }

    public function setIsLate(bool $isLate): static
    {
        $this->isLate = $isLate;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->Company;
    }

    public function setCompany(?Company $Company): static
    {
        $this->Company = $Company;

        return $this;
    }

    public function getAirportDeparture(): ?Airport
    {
        return $this->airportDeparture;
    }

    public function setAirportDeparture(?Airport $airportDeparture): static
    {
        $this->airportDeparture = $airportDeparture;

        return $this;
    }

    public function getAirportArrival(): ?Airport
    {
        return $this->airportArrival;
    }

    public function setAirportArrival(?Airport $airportArrival): static
    {
        $this->airportArrival = $airportArrival;

        return $this;
    }

    /**
     * @return Collection<int, Stop>
     */
    public function getStops(): Collection
    {
        return $this->stops;
    }

    public function addStop(Stop $stop): static
    {
        if (!$this->stops->contains($stop)) {
            $this->stops->add($stop);
            $stop->setFlight($this);
        }

        return $this;
    }

    public function removeStop(Stop $stop): static
    {
        if ($this->stops->removeElement($stop)) {
            // set the owning side to null (unless already changed)
            if ($stop->getFlight() === $this) {
                $stop->setFlight(null);
            }
        }

        return $this;
    }
}
