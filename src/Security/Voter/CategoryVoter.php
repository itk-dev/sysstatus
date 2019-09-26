<?php

namespace App\Security\Voter;

use App\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CategoryVoter extends AbstractVoter
{
    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return $this->isSupported($attribute, $subject, [Category::class]);
    }

    /**
     * @param string $attribute
     * @param Category $subject
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

        /* @var \Doctrine\Common\Collections\Collection $themes */
        $themes = $subject->getThemes();

        $allow = false;

        /* @var \App\Entity\Theme $theme */
        foreach ($themes as $theme) {
            if ($this->authorizationChecker->isGranted($attribute, $theme)) {
                $allow = true;
                break;
            }
        }

        return $allow;
    }
}
