<?php

namespace App\Security\Voter;

use App\Entity\Answer;
use App\Entity\Group;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnswerVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $this->isSupported($attribute, $subject, [Answer::class]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->userHasAccessDirectAccess($subject, $token)) {
            return true;
        }

        if ($attribute == self::SHOW) {
            return true;
        }

        $report = $subject->getReport();
        $system = $subject->getSystem();

        if ($system == null && $report == null) {
            return false;
        }

        $entity = $report == null ? $system : $report;

        $user = $token->getUser();
        $userGroups = $user->getGroups()->map(function (Group $group) {
            return $group->getId();
        })->getValues();
        $entityGroups = $entity->getGroups()->map(function (Group $group) {
            return $group->getId();
        })->getValues();

        $userInEntityGroup = count(array_intersect($userGroups, $entityGroups)) > 0;

        switch ($attribute) {
            case self::SHOW:
            case self::EDIT:
            case self::NEW:
            case self::DELETE:
                return $userInEntityGroup;
        }

        return false;
    }
}
