<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ApiResource]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDeparture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateArrival = null;

    #[ORM\ManyToOne(inversedBy: 'flights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $cityDeparture = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $cityArrival = null;

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
}
