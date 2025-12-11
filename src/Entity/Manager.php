<?php

namespace App\Entity;

use App\Repository\ManagerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManagerRepository::class)]
class Manager extends User
{
    /**
     * @var Collection<int, CompanyManager>
     */
    #[ORM\OneToMany(targetEntity: CompanyManager::class, mappedBy: 'manager')]
    private Collection $company;

    public function __construct()
    {
        $this->company = new ArrayCollection();
    }
    /**
     * @return Collection<int, CompanyManager>
     */
    public function getCompany(): Collection
    {
        return $this->company;
    }

    public function addCompany(CompanyManager $company): static
    {
        if (!$this->company->contains($company)) {
            $this->company->add($company);
            $company->setManager($this);
        }

        return $this;
    }

    public function removeCompany(CompanyManager $company): static
    {
        if ($this->company->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getManager() === $this) {
                $company->setManager(null);
            }
        }

        return $this;
    }
}
