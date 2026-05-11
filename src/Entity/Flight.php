<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
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
use ApiPlatform\Metadata\QueryParameter;
use App\Dto\FlightRequestDto;
use App\State\CustomFlightsGetCollection;
use App\State\InsertFlightStateProcessor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\State\CustomGetCollectionAvailableFlightsProvider;
use App\State\FlightStateProvider;
use App\State\UpdateFlightProcessor;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ApiResource( // Déclaration de l'entité Flight comme ressource de l'API
    security: "is_granted('ROLE_ADMIN')", // seul un utilisateur au rôle Admin peut avoir accès à toutes les opérations d'une ressource de type Flight
    securityMessage: 'Accès interdit car vous n\'êtes pas admin',
    operations: [
        new Get( // récuperer une ressource vol d'avion à l'aide de son ID
            provider: FlightStateProvider::class // traitement personnalisé pour récupérer un vol
        ),
        new GetCollection( // récuperer l'ensemble des ressources de type vol d'avion dans le serveur
            security: 'is_granted("PUBLIC_ACCESS")', // les utilisateurs non connectés peuvent avoir accès à l'ensemble des vols disponibles
            provider: CustomFlightsGetCollection::class, // traitement personnalisé pour récupérer tous les vols
            // Ajout de filtres personnalisés sur l'endpoint /api/flight
            parameters: [
                'company.name' => new QueryParameter(
                    property: 'company.name',
                    filter: new PartialSearchFilter()
                ),
                'airportDeparture.city.name' => new QueryParameter(
                    property: 'airportDeparture.city.name',
                    filter: new PartialSearchFilter()
                ),
                'airportArrival.city.name' => new QueryParameter(
                    property: 'airportArrival.city.name',
                    filter: new PartialSearchFilter()
                ),
                'status.name' => new QueryParameter(
                    property: 'status.name',
                    filter: new ExactFilter()
                ),
                'dateDeparture' => new QueryParameter(
                    filter: new DateFilter(),
                    property: 'dateDeparture'
                ),
                'dateArrival' => new QueryParameter(
                    filter: new DateFilter(),
                    property: 'dateArrival'
                )
            ]
        ),
        // récuperer l'ensemble des ressources de type vol d'avion présent dans le serveur
        new GetCollection(
            // récuperer l'ensemble des ressources de type vol d'avion disponibles dans le serveur
            uriTemplate: '/get-available-flights', // création d'une route personnalisée (endpoint)
            name: 'getAvailableFlights',
            provider: CustomGetCollectionAvailableFlightsProvider::class,
            security: 'is_granted("PUBLIC_ACCESS")', // les utilisateurs non connectés peuvent avoir accès à l'ensemble des vols disponibles
        ),
        new Post(
            // créer une nouvelle ressource vol d'avion
            processor: InsertFlightStateProcessor::class,
            input: FlightRequestDto::class,
        ),
        new Patch( // modifier une ressource vol d'avion à l'aide de son ID
            input: FlightRequestDto::class,
            processor: UpdateFlightProcessor::class // traitement personnalisé de mise à jour de vol à l'aide de son ID
        ),
        new Delete() // supprimer une ressource vol d'avion à l'aide de son ID
    ]
)]
// #[ApiFilter( // mise en place de filtre de recherche d'occurences avec une stratégie partielle 
//     SearchFilter::class,
//     properties: [
//         'company' => 'partial'
//     ]
// )]
// #[ApiFilter( // mise en place de filtre de dates
//     DateFilter::class,
//     properties: ['dateDeparture', 'dateArrival']
// )]
// #[ApiFilter(
//     SearchFilter::class,
//     properties: [
//         'status.name' => 'exact'
//     ]
// )]
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

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Assert\NotBlank(message: "Le prix d'une réservation est obligatoire")]
    #[Assert\PositiveOrZero(message: 'Le prix doit être supérieur ou égal à zéro')] // le prix peut être égal à zéro dans certains cas par exemple après un avoir ou une promo
    private ?string $price = null;


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
    private ?Company $company = null;

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

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

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
    public function setDateDeparture(\DateTimeInterface $dateDeparture)
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
    public function setDateArrival(\DateTimeInterface $dateArrival)
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
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }
}
