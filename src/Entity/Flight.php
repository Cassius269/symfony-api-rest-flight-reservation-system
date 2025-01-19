<?php

namespace App\Entity;

use App\Repository\FlightRepository;
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
}
