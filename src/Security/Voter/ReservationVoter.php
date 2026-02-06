<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ReservationVoter extends Voter
{
    public const EDIT = 'RESERVATION_EDIT';
    public const VIEW = 'RESERVATION_VIEW';

    // Injection de dépendances
    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager
    ) {}
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
                if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN']) || $subject->getPassenger()->getEmail() === $user->getEmail()) {
                    return true;
                }
                break;
            case self::VIEW:
                // dd($subject);
                // si l'utilisateur est un ADMIN ou propriétaire de la réservation, il a la permission de regarder une réservation
                if (
                    $this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])
                    || $subject->getPassenger()->getEmail() === $user->getEmail()
                ) {
                    return true;
                }
                break;
        }

        return false;
    }
}
