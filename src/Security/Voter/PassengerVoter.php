<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class PassengerVoter extends Voter
{
    public const EDIT = 'PASSENGER_EDIT';
    public const VIEW = 'PASSENGER_VIEW';
    public const DELETE = 'PASSENGER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Passenger;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // si l'utilisateur est un ADMIN ou auteur du profil, il a la permission d'accéder à des informations personnelles
                if (in_array("ROLE_ADMIN", $user->getRoles()) || $subject->getEmail() === $user->getUserIdentifier()) {
                    return true;
                }
                return true;
                break;
            case self::VIEW:
                // si l'utilisateur est un ADMIN ou auteur du profil, il a la permission d'accéder à des informations personnelles
                if (in_array("ROLE_ADMIN", $user->getRoles()) || $subject->getEmail() === $user->getUserIdentifier()) {
                    return true;
                }
                break;
            case self::DELETE:
                // si l'utilisateur est un ADMIN ou auteur du profil, il a la permission de supprimer ses informations personnelles
                if (in_array("ROLE_ADMIN", $user->getRoles()) || $subject->getEmail() === $user->getUserIdentifier()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
