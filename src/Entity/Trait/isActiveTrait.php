<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait isActiveTrait {
    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}