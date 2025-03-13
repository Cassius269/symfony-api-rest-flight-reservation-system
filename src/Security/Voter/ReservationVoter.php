<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ReservationVoter extends Voter
{
    public const EDIT = 'RESERVATION_EDIT';
    public const VIEW = 'RESERVATION_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Reservation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // dd($subject->getPassenger()->getEmail() === $user->getEmail());
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // si l'utilisateur est un ADMIN ou propriétaire de la réservation, il a la permission de modifier une réservation
                if (in_array("ROLE_ADMIN", $user->getRoles()) || $subject->getPassenger()->getEmail() === $user->getEmail()) {
                    return true;
                }
                break;
            case self::VIEW:
                // si l'utilisateur est un ADMIN ou propriétaire de la réservation, il a la permission de regarder une réservation
                if (in_array("ROLE_ADMIN", $user->getRoles()) || $subject->getPassenger()->getEmail() === $user->getEmail()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
