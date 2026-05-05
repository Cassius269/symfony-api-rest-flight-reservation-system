<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SuccessAuthenticationSubscriber implements EventSubscriberInterface
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {

        // Récupérer l'utilisateur
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // dd($user);

        $data = $event->getData();

        $data = [
            'id' => $user instanceof User ? $user->getId() : null,
            'firstname' => $user instanceof User ? $user->getFirstname() : null,
            'lastname' => $user instanceof User ? $user->getLastname() : null,
            'email' => $user->getUserIdentifier(),
            // 'roles' => $user instanceof User ? $user->getRoles() : []
        ];

        // Charger les données à jour à l'évenement
        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}
