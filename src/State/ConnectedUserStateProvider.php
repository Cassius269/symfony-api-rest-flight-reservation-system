<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserResponseDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConnectedUserStateProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {

        $user = $this->security->getUser();
        // dd($user);

        if (!$user) {
            return new NotFoundHttpException(message: 'Aucun utilisateur connecté trouvé');
        }


        // Préparer la réponse à retourner au client
        $connectedUserDto = new UserResponseDto;
        $connectedUserDto->id = $user->getId();
        $connectedUserDto->firstname = $user->getFirstname();
        $connectedUserDto->lastname = $user->getLastname();
        $connectedUserDto->email = $user->getEmail();

        // Trouver un moyen de rendre minuscule le rôle et exclure la partie "ROLE_" dans "ROLE_ROLE-ATTRIBUÉ
        $connectedUserDto->role = $user->getRoles()[0];

        return $connectedUserDto;
    }
}
