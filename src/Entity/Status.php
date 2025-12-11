<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message: 'Le nom du status est obligatoire')]
    #[Assert\Length(
        min: 4, 
        max: 15, 
        minMessage: 'Le nom du status doit avoir au moins 4 caractères',
        maxMessage: 'Le nom du status doit avoir moins de 15 caractères'

    )]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
