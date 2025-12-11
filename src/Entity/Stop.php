<?php

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StopRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StopRepository::class)]
class Stop
{
    // Importer le trait des dates de création et mise à jour
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date de départ départ est obligatoire')]
    #[Assert\DateTime(message: "La date doit être au format Y-m-d H:i:s")]
    private ?\DateTime $dateDeparture = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La date d\'arrivée départ est obligatoire')]
    #[Assert\DateTime(message: "La date doit être au format Y-m-d H:i:s")]
    private ?\DateTime $dateArrival = null;

    #[ORM\ManyToOne(inversedBy: 'stops')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le vol associé à l\'escale est obligatoire')]
    private ?Flight $flight = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'L\'aéroport d\'arrivée départ est obligatoire')]
    private ?Airport $airportArrival = null;

    #[ORM\Column]
    private ?int $orderFlight = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\ManyToMany(targetEntity: Reservation::class, inversedBy: 'stops')]
    private Collection $reservation;

    public function __construct()
    {
        $this->reservation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeparture(): ?\DateTime
    {
        return $this->dateDeparture;
    }

    public function setDateDeparture(\DateTime $dateDeparture): static
    {
        $this->dateDeparture = $dateDeparture;

        return $this;
    }

    public function getDateArrival(): ?\DateTime
    {
        return $this->dateArrival;
    }

    public function setDateArrival(\DateTime $dateArrival): static
    {
        $this->dateArrival = $dateArrival;

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

    public function getAirportArrival(): ?Airport
    {
        return $this->airportArrival;
    }

    public function setAirportArrival(?Airport $airportArrival): static
    {
        $this->airportArrival = $airportArrival;

        return $this;
    }

    public function getOrderFlight(): ?int
    {
        return $this->orderFlight;
    }

    public function setOrderFlight(int $orderFlight): static
    {
        $this->orderFlight = $orderFlight;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        $this->reservation->removeElement($reservation);

        return $this;
    }
}
