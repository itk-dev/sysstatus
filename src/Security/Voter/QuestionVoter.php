<?php

namespace App\Security\Voter;

use App\Entity\Question;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuestionVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $this->isSupported($attribute, $subject, [Question::class]);
    }

    /**
     * @param string $attribute
     * @param Question $subject
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->userHasAccessDirectAccess($subject, $token)) {
            return true;
        }

        if ($attribute == self::SHOW) {
            return true;
        }

        /* @var \App\Entity\Theme $theme */
        $category = $subject->getCategory();

        if ($category == null) {
            return false;
        }

        return $this->authorizationChecker->isGranted($attribute, $category);
    }
}
