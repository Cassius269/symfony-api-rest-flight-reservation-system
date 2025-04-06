<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CopilotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CopilotRepository::class)]
#[ApiResource]
class Copilot extends User
{
    /**
     * @var Collection<int, Flight>
     */
    #[ORM\ManyToMany(targetEntity: Flight::class, mappedBy: 'copilots')]
    private Collection $flights;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
    }

    /**
     * @return Collection<int, Flight>
     */
    public function getFlights(): Collection
    {
        return $this->flights;
    }

    public function addFlight(Flight $flight): static
    {
        if (!$this->flights->contains($flight)) {
            $this->flights->add($flight);
            $flight->addCopilot($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            $flight->removeCopilot($this);
        }

        return $this;
    }
}
