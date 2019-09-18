<?php

namespace App\Security\Voter;

use App\Entity\Report;
use App\Entity\System;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReportSystemVoter extends Voter
{
    const SHOW = 'show';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::SHOW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Report && !$subject instanceof System) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // Admins can access all.
        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $userGroups = array_map(function($e) {
            return is_object($e) ? $e->getId() : null;
        }, $user->getGroups()->toArray());
        $entityGroups = array_map(function($e) {
            return is_object($e) ? $e->getId() : null;
        }, $subject->getGroups()->toArray());

        switch ($attribute) {
            case self::SHOW:
                if (count(array_intersect($userGroups, $entityGroups)) > 0) {
                    return true;
                }
                break;
            case self::EDIT:
                if (count(array_intersect($userGroups, $entityGroups)) > 0) {
                    return $user->hasRole('ROLE_GROUP_ADMIN');
                }

                break;
        }

        return false;
    }
}
