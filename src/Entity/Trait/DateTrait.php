<?php

// Ce trait permet de réutiliser les propriétés et méthodes des dates de création et de mise à jour

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Attribute\Groups;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait DateTrait
{
    #[ORM\Column]
    // #[Assert\DateTime(message: "La date doit être au format Y-m-d H:i:s")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\DateTime(message: "La date doit être au format Y-m-d H:i:s")]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
