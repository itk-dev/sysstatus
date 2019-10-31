<?php

namespace App\Security\Voter;

use App\Entity\ThemeCategory;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ThemeCategoryVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $this->isSupported($attribute, $subject, [ThemeCategory::class]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->userHasAccessDirectAccess($subject, $token)) {
            return true;
        }

        if ($attribute == self::SHOW) {
            return true;
        }

        /* @var \App\Entity\Theme $theme */
        $theme = $subject->getTheme();

        return $this->authorizationChecker->isGranted($attribute, $theme);
    }
}
