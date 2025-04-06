<?php

namespace App\Dto;

use App\Dto\CityRequestDto;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

// Création d'un DTO pour le recueille des informations de la requête de création d'un nouveau vol d'avion

class FlightRequestDto
{
    #[Assert\NotBlank(message: "La ville de départ doit être renseignée")]
    #[Assert\Valid] // Valide aussi les sous-objets (CityRequestDto)    
    private ?CityRequestDto $cityDeparture = null; // ville de départ

    #[Assert\NotBlank(message: "La ville d'arrivée doit être renseignée")]
    #[Assert\Valid] // Valide aussi les sous-objets (CityRequestDto)  
    private ?CityRequestDto $cityArrival = null; // ville d'arrivée

    public ?int $airplaneId = null;

    #[Assert\NotBlank(message: "La date de départ doit être renseignée")]
    public ?DateTime $dateDeparture = null;

    #[Assert\NotBlank(message: "La date d'arrivée doit être renseignée")]
    public ?DateTime $dateArrival = null;

    public ?CaptainRequestDto $captain = null;
    public ?array $copilots = null;
    /* Exceptionnement j'ai rajouté les accesseurs des villes de destination et de départ 
    pour manipuler facilement les données imbriquées des vols renseignés côtés cliens 
    */


    /**
     * Get the value of cityDeparture
     */
    public function getCityDeparture()
    {
        return $this->cityDeparture;
    }

    /**
     * Get the value of cityArrival
     */
    public function getCityArrival()
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
}
