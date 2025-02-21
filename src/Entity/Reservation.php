<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\ReservationRequestDto;
use App\Repository\ReservationRepository;
use App\State\ReservationStateProcessor;
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
#[ApiResource(
    operations: [
        new Get(), // récupérer une ressource de type Réservation à l'aide de son ID
        new GetCollection(), // récupérer l'ensemble des ressources de type Réservation
        new Post(
            // envoyer une nouvelle ressource Réservation au serveur
            processor: ReservationStateProcessor::class, // traitement des données entrantes pour création de nouvelle ressource
            input: ReservationRequestDto::class
        ),
        new Patch(), // mettre à jour une ressource Réservation partiellement
        new Delete // supprimer une ressource Réservation à l'aide de son ID
    ]
)]
class Reservation
{
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
    private ?string $numberFlightSeat = null; // chaque passager un numéro unique 

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Assert\NotBlank(message: "Le prix d'une réservation est obligatoire")]
    #[Assert\PositiveOrZero(message: 'Le prix doit être supérieur ou égal à zéro')] // le prix peut être égal à zéro dans certains cas par exemple après un avoir ou une promo
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le passager doit être renseigné ")]
    private ?Passenger $passenger = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Les informations sur le vol sont obligatoires")]
    private ?Flight $flight = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La date de création de la donnée est obligatoire")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

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
}
