<?php

namespace App\Security;

use App\Entity\Advert;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdvertVoter extends Voter
{
    const EDIT = 'ADVERT_EDIT';
    const DELETE = 'ADVERT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Advert) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Advert $advert */
        $advert = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($advert, $user),
            self::DELETE => $this->canDelete($advert, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Advert $advert, User $user): bool
    {
        // Managers and Admins can edit any advert
        if (in_array('ROLE_MANAGER', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Users can only edit their own adverts
        return $user === $advert->getUser();
    }

    private function canDelete(Advert $advert, User $user): bool
    {
        // Managers and Admins can delete any advert
        if (in_array('ROLE_MANAGER', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Users can only delete their own adverts
        return $user === $advert->getUser();
    }
}
