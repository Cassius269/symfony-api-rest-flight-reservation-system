<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Dto\CaptainRequestDto;
use App\Dto\CaptainResponseDto;
use App\Entity\User;
use App\Repository\CaptainRepository;
use App\State\CaptainStateProcessor;
use App\State\CaptainStateProvider;
use App\State\CustomCaptainsGetCollectionStateProvider;
use App\State\DeleteCaptainStateProcessor;
use App\State\UpdateCaptainStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaptainRepository::class)]
#[ApiResource( // Déclarer Commandant de bord en tant que ressource de l'API
    operations: [
        new Get( // rendre accessible une ressource grâce à son ID 
            provider: CaptainStateProvider::class, // traitement personnalisée de la réponse de l'endpoint
            output: CaptainResponseDto::class,
        ),
        new GetCollection( // rendre accessible l'ensemble des ressources 
            provider: CustomCaptainsGetCollectionStateProvider::class, // traitement personnalisé de l'endpoint de récupération de tous les commandants de bord
        ),
        new Post( // créer une nouvelle ressource 
            processor: CaptainStateProcessor::class,
            input: CaptainRequestDto::class,
            security: 'is_granted("ROLE_ADMIN")',
            securityMessage: 'Accès réfusé. Vous n\'êtes pas admin'
        ),
        new Patch( // mettre à jour une ressource en particulier de façon partielle 
            processor: UpdateCaptainStateProcessor::class // traitement personnalisé de la mise à jour d'une ressource de type commandant de bord
        ),
        new Delete( // supprimer une ressource Commandant de bord 
            processor: DeleteCaptainStateProcessor::class
        )
    ]
)]
class Captain extends User
{
    /**
     * @var Collection<int, Flight>
     */
    #[ORM\OneToMany(targetEntity: Flight::class, mappedBy: 'captain')]
    private Collection $flights;

    /**
     * @var Collection<int, CompanyCaptain>
     */
    #[ORM\OneToMany(targetEntity: CompanyCaptain::class, mappedBy: 'captain')]
    private Collection $companyCaptains;

    public function __construct()
    {
        $this->flights = new ArrayCollection();
        $this->companyCaptains = new ArrayCollection();
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
            $flight->setCaptain($this);
        }

        return $this;
    }

    public function removeFlight(Flight $flight): static
    {
        if ($this->flights->removeElement($flight)) {
            // set the owning side to null (unless already changed)
            if ($flight->getCaptain() === $this) {
                $flight->setCaptain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompanyCaptain>
     */
    public function getCompanyCaptains(): Collection
    {
        return $this->companyCaptains;
    }

    public function addCompanyCaptain(CompanyCaptain $companyCaptain): static
    {
        if (!$this->companyCaptains->contains($companyCaptain)) {
            $this->companyCaptains->add($companyCaptain);
            $companyCaptain->setCaptain($this);
        }

        return $this;
    }

    public function removeCompanyCaptain(CompanyCaptain $companyCaptain): static
    {
        if ($this->companyCaptains->removeElement($companyCaptain)) {
            // set the owning side to null (unless already changed)
            if ($companyCaptain->getCaptain() === $this) {
                $companyCaptain->setCaptain(null);
            }
        }

        return $this;
    }
}
