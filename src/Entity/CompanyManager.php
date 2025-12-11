<?php

namespace App\Entity;

use App\Entity\Trait\PeriodTrait;
use App\Repository\CompanyManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyManagerRepository::class)]
// Entité représentant les relations entre une compagnie et ses managers dans le temps
class CompanyManager
{
    // importer le trait des date de début et fin
    use PeriodTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'company')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "Le responsable doit être renseigné")]
    private ?Manager $manager = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): static
    {
        $this->manager = $manager;

        return $this;
    }
}
