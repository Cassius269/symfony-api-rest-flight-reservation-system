<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_departure = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_arrival = null;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city_departure = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city_arrival = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'flight', orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeparture(): ?\DateTimeInterface
    {
        return $this->date_departure;
    }

    public function setDateDeparture(\DateTimeInterface $date_departure): static
    {
        $this->date_departure = $date_departure;

        return $this;
    }

    public function getDateArrival(): ?\DateTimeInterface
    {
        return $this->date_arrival;
    }

    public function setDateArrival(\DateTimeInterface $date_arrival): static
    {
        $this->date_arrival = $date_arrival;

        return $this;
    }

    public function getCityDeparture(): ?city
    {
        return $this->city_departure;
    }

    public function setCityDeparture(?city $city_departure): static
    {
        $this->city_departure = $city_departure;

        return $this;
    }

    public function getCityArrival(): ?City
    {
        return $this->city_arrival;
    }

    public function setCityArrival(?City $city_arrival): static
    {
        $this->city_arrival = $city_arrival;

        return $this;
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
}
