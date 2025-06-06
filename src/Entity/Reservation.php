<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use App\Entity\Trait\DateTrait;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Dto\ReservationRequestDto;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\ReservationStateProvider;
use App\State\ReservationStateProcessor;
use App\Repository\ReservationRepository;
use App\State\UpdateReservationProcessor;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\State\CustomReservationGetCollectionStateProvider;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[UniqueEntity( // dans un même vol, un siège ne peut pas être occupé par plusieurs passagers
    fields: ['numberFlightSeat', 'flight'],
    message: 'Le siège {{ value }} est déjà réservé.',
)]
#[UniqueEntity( // un passager avec le même email ne peut pas réserver deux fois pour le même voyage
    fields: ['passenger', 'flight'],
    message: 'Le passager {{ value }} a déjà réservé pour ce vol.',
)]
#[UniqueEntity( // le PNR est unique
    fields: ['passengerNameRecord'],
    message: 'Un PNR similaire existe déjà'
)]
#[ApiResource(
    operations: [
        new Get( // récupérer une ressource de type Réservation à l'aide de son ID
            provider: ReservationStateProvider::class,
        ),
        new GetCollection( // récupérer l'ensemble des ressources de type Réservation
            paginationEnabled: true, // pagination de la data activée par défaut
            paginationItemsPerPage: 20,  // définir le nombre de ressources réservation à afficher par page, 
            paginationClientEnabled: true, // donner la possibilité au client de choisir l'activation de la pagination
            paginationClientItemsPerPage: true, // donner la possibilité au client de choisir le nombre d'objets ressources par page, 
            provider: CustomReservationGetCollectionStateProvider::class,
            security: "is_granted('ROLE_ADMIN')", // seul un utilisateur au rôle Admin peut avoir accès aux réservations
            securityMessage: 'Vous n\'êtes pas Admin, Vous n\'êtes pas autorisé à accéder à ces ressources'
        ),
        new Post(
            // envoyer une nouvelle ressource Réservation au serveur
            processor: ReservationStateProcessor::class, // traitement des données entrantes pour création de nouvelle ressource
            input: ReservationRequestDto::class,
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PASSENGER')", // seul un utilisateur au rôle Admin  ou passager peut avoir accès à l'endpoint de création d'une nouvelle réservation
        ),
        new Patch( // mettre à jour une ressource Réservation partiellement
            // security: "is_granted('RESERVATION_EDIT', object)", // utilisation de voter personnalisé pour gérer la permission de modification d'une réservation
            securityMessage: 'Désolé, vous êtes ni admin ni le propriétaire de la réservation',
            processor: UpdateReservationProcessor::class,
            input: ReservationRequestDto::class
        ),
        new Delete( // supprimer une ressource Réservation à l'aide de son ID
            security: "is_granted('ROLE_ADMIN')", // par défaut seul un utilisateur au rôle Admin peut supprimer une réservation
            securityMessage: 'accès non autorisé'
        )
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'passengerNameRecord' => 'exact'
    ]
)]
class Reservation
{
    use DateTrait; // intégrer le trait des dates de créations et de mise à jour

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    #[Assert\NotBlank(message: "Le numéro de siège est obligatoire")]
    #[Assert\Regex(
        pattern: '/\d{1,3}[A-Z]$/',
        message: 'Le numéro de siège doit être une chaîne alphanumérique de 1 à 4 caractères au format nombre(s)-lettre.'
    )]
    private ?string $numberFlightSeat = null; // chaque passager d'un vol un numéro de siège unique 

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Assert\NotBlank(message: "Le prix d'une réservation est obligatoire")]
    #[Assert\PositiveOrZero(message: 'Le prix doit être supérieur ou égal à zéro')] // le prix peut être égal à zéro dans certains cas par exemple après un avoir ou une promo
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le passager doit être renseigné ")]
    private ?Passenger $passenger = null;

    #[ORM\Column(length: 6, nullable: false)]
    #[Assert\NotBlank(message: "La référence PNR est obligatoire")]
    private ?string $passengerNameRecord = null; // Passenger Name Record appelé aussi PNR est une réference unique de chaque réservation à un vol

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Les informations sur le vol sont obligatoires")]
    private ?Flight $flight = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberFlightSeat(): ?string
    {
        return $this->numberFlightSeat;
    }

    public function setNumberFlightSeat(string $numberFlightSeat): static
    {
        $this->numberFlightSeat = $numberFlightSeat;

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

    public function getPassenger(): ?Passenger
    {
        return $this->passenger;
    }

    public function setPassenger(?Passenger $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(?Flight $flight): static
    {
        $this->flight = $flight;

        return $this;
    }

    public function getPassengerNameRecord(): ?string
    {
        return $this->passengerNameRecord;
    }

    public function setPassengerNameRecord(string $passengerNameRecord): static
    {
        $this->passengerNameRecord = $passengerNameRecord;

        return $this;
    }
}
