<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ThemeManager
{
    /**
     * ThemeManager constructor.
     */
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly CategoryRepository $categoryRepository
    )
    {
    }

    /**
     * @return array<string>
     */
    public function getCategoriesForCurrentUser(): array
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $roles = $user->getRoles();

        $userCategories = [];

        if (in_array('ROLE_ADMIN', $roles, true) || in_array('ROLE_SUPER_ADMIN', $roles, true)) {
            $adminCategories = $this->categoryRepository->findAll();
            foreach ($adminCategories as $category) {
                $userCategories[$category->getName()] = $category;
            }

            return $userCategories;
        } else {
            $createdCategories = $this->categoryRepository->findBy([
                'createdBy' => $user->getUsername(),
            ]);

            foreach ($createdCategories as $category) {
                $userCategories[$category->getName()] = $category;
            }
        }

        $userGroups = $user->getGroups();
        $userThemes = [];

        /* @var Group $userGroup */
        foreach ($userGroups as $userGroup) {
            foreach ($userGroup->getSystemThemes() as $theme) {
                $userThemes[$theme->getId()] = $theme;
            }
            foreach ($userGroup->getReportThemes() as $theme) {
                $userThemes[$theme->getId()] = $theme;
            }
        }

        foreach ($userThemes as $theme) {
            /* @var Category $category */
            foreach ($theme->getOrderedCategories() as $category) {
                $userCategories[$category->getName()] = $category;
            }
        }

        return $userCategories;
    }
}
