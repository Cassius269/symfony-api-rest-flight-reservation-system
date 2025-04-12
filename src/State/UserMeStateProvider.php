<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserResponseDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Entity\User;

class UserMeStateProvider implements ProviderInterface
{
    // Injection de dépendance(s)
    public function __construct(
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedHttpException('Accès refusé. Aucun utilisateur authentifié');
        }

        // Préparer la réponse à retourner au client: navigateur, postman,etc
        $userDto = new UserResponseDto;
        $userDto->id = $user->getId();
        $userDto->firstname = $user->getFirstname();
        $userDto->lastname = $user->getLastname();
        $userDto->email = $user->getEmail();
        $userDto->roles = $user->getRoles();
        $userDto->createdAt = $user->getCreatedAt();
        $userDto->updtatedAt = $user->getUpdatedAt();

        return $userDto;
    }
}
