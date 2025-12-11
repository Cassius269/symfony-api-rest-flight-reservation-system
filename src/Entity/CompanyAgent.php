<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\PeriodTrait;
use App\Entity\Trait\isActiveTrait;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyAgentRepository;
use Symfony\Component\Validator\Constraints as Assert;

// Entité représentant les relations entre une compagnie et ses agents dans le temps
#[ORM\Entity(repositoryClass: CompanyAgentRepository::class)]
#[ApiResource]
class CompanyAgent
{
    // importer le trait des date de début et fin
    use PeriodTrait;

    // importer le trait de vérification de situation d'activité d'un agent
    use isActiveTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank( message: 'La compagnie doit être renseignée')]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank( message: 'L\'agent doit être renseigné')]
    private ?Agent $agent = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;

        return $this;
    }
}
