<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PassengerRepository;

#[ORM\Entity(repositoryClass: PassengerRepository::class)]
class Passenger extends User {}
