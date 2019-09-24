<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Group;
use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ThemeManager {
    private $tokenStorage;
    private $categoryRepository;

    /**
     * ThemeManager constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, CategoryRepository $categoryRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoriesForCurrentUser() {
        $user = $this->tokenStorage->getToken()->getUser();

        $userCategories = [];

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            $adminCategories = $this->categoryRepository->findAll();
            foreach ($adminCategories as $category) {
                $userCategories[$category->getName()] = $category;
            }

            return $userCategories;
        }
        else {
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
