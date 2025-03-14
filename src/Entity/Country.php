<?php

namespace App\Entity;


use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use App\Entity\Trait\DateTrait;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CountryRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\State\CustomCountriesGetCollectionStateProvider;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[UniqueEntity(
    fields: 'name',
    message: 'Le nom de pays {{ value }} existe déjà'
)]
#[UniqueEntity(
    fields: 'isoCode',
    message: 'Le code ISO {{ value }} existe déjà'
)]
#[
    ApiResource( // Transformer l'entité Country en une ressource API, avec toutes les opérations CRUD
        security: "is_granted('ROLE_ADMIN')", // par défaut, seul un utilisateur au rôle Admin peut avoir accès à toutes les opérations d'une ressource
        operations: [
            new Get(), // rendre accessible une ressource grâce à son ID 
            new GetCollection( // rendre accessible l'ensemble des ressources 
                provider: CustomCountriesGetCollectionStateProvider::class,
                security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PASSENGER')", // seuls des utilisateurs au rôle Admin ou Passager peut avoir accès à cet endpoint traité avec un provider
            ),
            new Post(), // créer une nouvelle ressource 
            new Patch(), // mettre à jour partiellement une ressource grâce à l'ID
            new Delete() // supprimer une ressource de type pays à l'aide de son ID
        ]
    )
]
class Country
{
    use DateTrait; // intégrer le trait des dates de créations et de mise à jour

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom du pays est obligatoire')]
    #[Groups(['flight:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 3, nullable: true)]
    #[Assert\NotBlank(message: 'Le code ISO du pays est obligatoire')]
    #[Assert\Length(
        max: 3,
        maxMessage: 'Le code ISO du pays doit contenir {{ limit }} caractères'
    )]
    private ?string $isoCode = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La timezone du pays est obligatoire')]
    #[Assert\Length(
        max: 15,
        maxMessage: 'La timezone est trop longue'
    )]
    private ?string $timezone = null;


    /**
     * @var Collection<int, City>
     */
    #[ORM\OneToMany(targetEntity: City::class, mappedBy: 'country', orphanRemoval: true)]
    private Collection $cities;

    #[ORM\ManyToOne(inversedBy: 'countries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Continent $continent = null;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
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

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(?string $isoCode): static
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): static
    {
        if (!$this->cities->contains($city)) {
            $this->cities->add($city);
            $city->setCountry($this);
        }

        return $this;
    }

    public function removeCity(City $city): static
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountry() === $this) {
                $city->setCountry(null);
            }
        }

        return $this;
    }

    // Cette méthode permet de stringifier l'objet Country
    public function __toString(): string
    {
        return $this->getName(); // Supposons que la propriété s'appelle 'name'
    }

    public function getContinent(): ?Continent
    {
        return $this->continent;
    }

    public function setContinent(?Continent $continent): static
    {
        $this->continent = $continent;

        return $this;
    }
}
