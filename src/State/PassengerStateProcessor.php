<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\PassengerResponseDto;
use App\Entity\Passenger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Ce processor utilise le principe de Data Transfert Object (pas besoin de groupe de sérialisation avec les DTO)
// Finalement seul le processeur InsertPassengerProcessor sans DTO est utilisé
class PassengerStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PassengerRepository $passengerRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($data);

        // Rechercher un passager similaire
        $isSimilarPassenger = $this->passengerRepository->findOneByEmail($data->email); // Vérifier la présence d'un auteur ayant le même email se trouvant dans la charge utile de la requête POST

        if ($isSimilarPassenger) {
        };

        // Si aucun utilisateur n'utilise le mail
        if (!isset($data->firstname) || !isset($data->lastname) || !isset($data->email) || !isset($data->firstname)) {
            dd('Requête mal formulée');
        }
        // Créer un nouvel objet ressource Passenger
        $passenger = new Passenger;
        $passenger->setCreatedAt(new \DateTimeImmutable())
            ->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setEmail($data->email)
            ->setRoles(["ROLE_PASSENGER"]);

        // Hasher le mot de passe 
        $plainTextPassword = $data->password;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $passenger,
            $plainTextPassword
        );

        // Stoker le mot de passe hashé dans le nouvel objet Passenger
        $passenger->setPassword($hashedPassword);

        // Enregistrer et envoyer en base de données le nouveau passager
        $this->entityManager->persist($passenger);
        $this->entityManager->flush();


        // Retourner au client le même DTO de requête car les informations à renvoyer sont les mêmes
        return $data;
    }
}
