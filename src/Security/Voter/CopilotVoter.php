<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

final class CopilotVoter extends Voter
{
    public const CREATE = 'COPILOT_CREAT';
    public const VIEW = 'COPILOT_VIEW';
    public const EDIT = 'COPILOT_EDIT';
    public const DELETE = 'COPILOT_DELETE';

    // Injection de dépendance(s)
    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof \App\Entity\Copilot;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser(); // utilisateur authentifié
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CREATE:
                // Vérifier si c'est un ADMIN
                if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
                    return true;
                }
                break;
            case self::EDIT:
                // Vérifier si le mail de l'utilisateur connecté correspond à l'email de l'objet à modifier ou si c'est un ADMIN
                if ($subject->getEmail() == $user->getEmail() || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
                    // dd($subject->getEmail());
                    return true;
                }
                break;
            case self::VIEW:
                // Vérifier si le mail de l'utilisateur connecté correspond à l'email de l'objet à modifier ou si c'est un ADMIN
                if ($subject->getEmail() == $user->getEmail() || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
                    return true;
                }
                break;
            case self::DELETE:
                // Vérifier si le mail de l'utilisateur connecté correspond à l'email de l'objet à modifier ou si c'est un ADMIN
                if ($subject->getEmail() == $user->getEmail() || $this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
                    return true;
                }
                break;
        }

        return false;
    }
}
