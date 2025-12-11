<?php

namespace App\Entity;

use App\Repository\FlightOperationOfficerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightOperationOfficerRepository::class)]
// Entitté représentant les agents d'une compagnie aérienne
class FlightOperationOfficer extends User {}
