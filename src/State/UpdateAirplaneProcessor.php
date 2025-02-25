<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

class UpdateAirplaneProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $data->setUpdatedAt(new \DateTime()); // mettre à jour la date de mise à la date du jour

        $this->entityManager->flush(); // envoyer la ressource de type avion à jour au serveur
        return $data; // renvoyer la donnée reçu en tant que résultat
    }
}
