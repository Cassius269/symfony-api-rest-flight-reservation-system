<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class CaptainVoter extends Voter
{
    public const EDIT = 'CAPTAIN_EDIT';
    public const VIEW = 'CAPTAIN_VIEW';
    public const DELETE = 'CAPTAIN_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Captain;
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
                // Vérifier si le mail de l'utilisateur connecté correspond à l'email de l'objet à modifier ou si c'est un ADMIN
                if ($subject->getEmail() == $user->getEmail() || in_array('ROLE_ADMIN', $subject->getRoles())) {
                    return true;
                }
                break;
            case self::VIEW:
                // Vérifier si le mail de l'utilisateur connecté correspond à l'email de l'objet à modifier ou si c'est un ADMIN
                if ($subject->getEmail() == $user->getEmail() || in_array('ROLE_ADMIN', $subject->getRoles())) {
                    return true;
                }
                break;
            case self::DELETE:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
