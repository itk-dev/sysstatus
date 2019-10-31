<?php

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\Theme;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ThemeVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $this->isSupported($attribute, $subject, [Theme::class]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->userHasAccessDirectAccess($subject, $token)) {
            return true;
        }

        if ($attribute == self::SHOW) {
            return true;
        }

        /* @var \Doctrine\Common\Collections\ArrayCollection $systemGroups */
        $systemGroups = $subject->getSystemGroups();
        /* @var \Doctrine\Common\Collections\Collection $reportGroups */
        $reportGroups = $subject->getReportGroups();

        if ($systemGroups == null && $reportGroups == null) {
            return false;
        }

        $entityGroups = $systemGroups->map(function (Group $group) {
            return $group->getId();
        })->getValues();
        $entityGroups = array_merge($entityGroups, $reportGroups->map(function (Group $group) {
            return $group->getId();
        })->getValues());

        $user = $token->getUser();
        $userGroups = $user->getGroups()->map(function (Group $group) {
            return $group->getId();
        })->getValues();

        $userInEntityGroup = count(array_intersect($userGroups, $entityGroups)) > 0;

        switch ($attribute) {
            case self::SHOW:
            case self::EDIT:
                return $userInEntityGroup;
            case self::DELETE:
            case self::NEW:
                return $userInEntityGroup && $user->hasRole('ROLE_GROUP_ADMIN');
        }

        return false;
    }
}
