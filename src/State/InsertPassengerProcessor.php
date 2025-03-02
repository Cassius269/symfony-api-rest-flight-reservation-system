<?php

namespace App\State;

use App\Entity\Passenger;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;

use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Ce processor n'utilise pas de DTO, il utilise directement l'entité, ensuite les groupes de sérialisation pour choisir les champs exposés à l'affichage du résultat
class InsertPassengerProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $processor
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        if (!$data instanceof Passenger) {
            throw new \InvalidArgumentException('L\'objet reccueilli n\'est pas une instance de la classe Passenger');
        }

        // Hasher le mot de passe du nouveau passager
        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->getPassword()
        );

        // Mettre à jour le mot de passe de l'utilisateur par celui qui est hashé
        $data->setPassword($hashedPassword);

        // Définir le rôle des passagers par défaut
        $data->setRoles(['ROLE_PASSENGER']);

        // Ajouter la date de création d'un nouvel utilisateur
        $data->setCreatedAt(new \DateTimeImmutable());

        // Ajoutez les groupes de sérialisation au contexte
        $context['groups'] = ['passenger:read'];

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
