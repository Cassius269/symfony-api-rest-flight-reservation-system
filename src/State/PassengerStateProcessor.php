<?php

namespace App\State;

use App\Entity\Passenger;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Service\HashPasswordService;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

// Ce processor utilise le principe de Data Transfert Object (pas besoin de groupe de sérialisation avec les DTO)
class PassengerStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PassengerRepository $passengerRepository,
        private HashPasswordService $hashPasswordService,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {


        // Si aucun utilisateur n'utilise le mail
        if (!isset($data->firstname) || !isset($data->lastname) || !isset($data->email) || !isset($data->firstname)) {
            throw new UnprocessableEntityHttpException(json_encode(
                [
                    'message' => 'réponse mal formatée'
                ]
            ));
        };
        // Créer un nouvel objet ressource Passenger
        $passenger = new Passenger;
        $passenger->setCreatedAt(new \DateTimeImmutable())
            ->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setEmail($data->email)
            ->setRoles(["ROLE_PASSENGER"]);

        // Hasher le mot de passe  et le stoker dans le nouvel objet Passenger depuis le service personnalisé
        $plainTextPassword = $data->password;
        $this->hashPasswordService->hashPassword($plainTextPassword, $passenger);

        // Vérifier les contraintes de validation avant d'envoyer la ressource au serveur
        $errors = $this->validator->validate($passenger);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Enregistrer et envoyer en base de données le nouveau passager
        $this->entityManager->persist($passenger);
        $this->entityManager->flush();


        // Retourner au client le même DTO de requête car les informations à renvoyer sont les mêmes
        $passengerResponseDto = new PassengerResponseDto;
        $passengerResponseDto->id = $passenger->getId();
        $passengerResponseDto->firstname = $passenger->getFirstname();
        $passengerResponseDto->lastname = $passenger->getLastname();
        $passengerResponseDto->email = $passenger->getEmail();

        return $passengerResponseDto;
    }
}
