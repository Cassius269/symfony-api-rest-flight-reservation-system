<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
class Agent extends User
{
    /**
     * @var Collection<int, CompanyAgent>
     */
    #[ORM\OneToMany(targetEntity: CompanyAgent::class, mappedBy: 'agent')]
    private Collection $companies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
    }

    /**
     * @return Collection<int, CompanyAgent>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(CompanyAgent $company): static
    {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
            $company->setAgent($this);
        }

        return $this;
    }

    public function removeCompany(CompanyAgent $company): static
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getAgent() === $this) {
                $company->setAgent(null);
            }
        }

        return $this;
    }
}
