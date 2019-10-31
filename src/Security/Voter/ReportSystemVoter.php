<?php

namespace App\Security\Voter;

use App\Entity\Report;
use App\Entity\System;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ReportSystemVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $this->isSupported($attribute, $subject, [Report::class, System::class]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->userHasAccessDirectAccess($subject, $token)) {
            return true;
        }

        $user = $token->getUser();
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
            case self::NEW:
            case self::DELETE:
                // Only Admins can add and delete reports and systems.
        }

        return false;
    }
}
