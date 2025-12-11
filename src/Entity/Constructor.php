<?php

namespace App\Entity;

use App\Entity\Trait\DateTrait;
use App\Repository\ConstructorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConstructorRepository::class)]
class Constructor
{
    // Importer le trait des dates de création et de mise à jour
    use DateTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;

    /**
     * @var Collection<int, AirplaneModel>
     */
    #[ORM\OneToMany(targetEntity: AirplaneModel::class, mappedBy: 'constructor')]
    private Collection $airplaneModels;

    public function __construct()
    {
        $this->airplaneModels = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, AirplaneModel>
     */
    public function getAirplaneModels(): Collection
    {
        return $this->airplaneModels;
    }

    public function addAirplaneModel(AirplaneModel $airplaneModel): static
    {
        if (!$this->airplaneModels->contains($airplaneModel)) {
            $this->airplaneModels->add($airplaneModel);
            $airplaneModel->setConstructor($this);
        }

        return $this;
    }

    public function removeAirplaneModel(AirplaneModel $airplaneModel): static
    {
        if ($this->airplaneModels->removeElement($airplaneModel)) {
            // set the owning side to null (unless already changed)
            if ($airplaneModel->getConstructor() === $this) {
                $airplaneModel->setConstructor(null);
            }
        }

        return $this;
    }
}
