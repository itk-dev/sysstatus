<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Report;
use App\Entity\System;
use App\Entity\Theme;
use App\Repository\CategoryRepository;
use App\Repository\ThemeCategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminController extends EasyAdminController
{
    private $categoryRepository;
    private $themeCategoryRepository;
    private $entityManager;
    private $paginator;

    /**
     * AdminController constructor.
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @param \App\Repository\ThemeCategoryRepository $themeCategoryRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ThemeCategoryRepository $themeCategoryRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->themeCategoryRepository = $themeCategoryRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * Overrides
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $entity = $this->entity;
        if ($entity['class'] == Report::class || $entity['class'] == System::class) {
            return $this->redirectToRoute('list', ['entityType' => strtolower($entity['name'])]);
        }

        return parent::listAction();
    }

    /**
     * Serves report and system lists.
     *
     * @Route("/list/{entityType}", name="list")
     *
     * @return mixed
     */
    public function list(Request $request, $entityType)
    {
        $queryParameters = $request->query;
        $formParameters = array_merge([
            'groups' => [],
            'subowner' => '',
            'search' => '',
        ], $queryParameters->get('form') ?: []);

        // Get the groups the user is added to.
        $userGroups = $this->getUser()->getGroups();
        $userGroupsThemesAndCategories = $this->getUserGroupsThemesAndCategories($userGroups->toArray());

        // Get a query for the entity type.
        $repository = $this->getRepository($entityType);
        $query = $repository->createQueryBuilder('e');
        $query = $this->applyFilters($query, $formParameters);

        // Get sub owners if a group has been selected.
        $subOwnerOptions = $this->getSubOwnerOptions($repository, $formParameters['groups']);

        $paginator = $this->paginator->paginate(
            $query,
            $queryParameters->get('page', 1),
            15
        );

        $filterFormBuilder = $this->getFilterFormBuilder($userGroupsThemesAndCategories,
            $formParameters, $subOwnerOptions, false, false);
        $filterFormBuilder->setMethod('GET')
            ->setAction($this->generateUrl('list',
                ['entityType' => $entityType]));

        // Generate render array.
        $items = [];
        switch ($entityType) {
            case 'system':
                /* @var System $item */
                foreach ($paginator->getItems() as $item) {
                    $items[$item->getId()] = [
                        'entity.system.id' => $item->getId(),
                        'entity.system.sys_title' => $item->getSysTitle(),
                        'entity.system.groups' => $item->getGroups() ? array_reduce($item->getGroups()->toArray(), function ($carry, Group $group) {
                            if (strlen($carry) > 0) {
                                $carry .= ', ';
                            }
                            $carry .= $group->getName();
                            return $carry;
                        }, '') : '',
                        'entity.system.sys_owner_sub' => $item->getSysOwnerSub(),
                        'entity.system.sys_system_owner' => $item->getSysSystemOwner(),
                        'entity.system.sys_link' => '<a href="'.$item->getSysLink().'">Link</a>',
                        'entity.system.selfServiceAvailableFromItems' => array_reduce($item->getSelfServiceAvailableFromItems()
                            ->toArray(), function ($carry, $item) {
                            $carry = (strlen($carry) > 0) ? $carry.', '.$item : $item;

                            return $carry;
                        }, ''),
                        'entity.system.text' => $item->getTextSet() ? '<label class="label label-success">Ja</label>' : '',
                    ];
                }
                break;
            case 'report':
                /* @var Report $item */
                foreach ($paginator->getItems() as $item) {
                    $items[$item->getId()] = [
                        'entity.report.id' => $item->getId(),
                        'entity.report.sys_title' => $item->getSysTitle(),
                        'entity.report.groups' => $item->getGroups() ? array_reduce($item->getGroups()->toArray(), function ($carry, Group $group) {
                            if (strlen($carry) > 0) {
                                $carry .= ', ';
                            }
                            $carry .= $group->getName();
                            return $carry;
                        }, '') : '',
                        'entity.report.sys_owner_sub' => $item->getSysOwnerSub(),
                        'entity.report.sys_system_owner' => $item->getSysSystemOwner(),
                        'entity.report.sys_link' => '<a href="'.$item->getSysLink().'">Link</a>',
                        'entity.report.text' => $item->getTextSet() ? '<label class="label label-success">Ja</label>' : '',
                    ];
                }
                break;
        }

        return $this->render('list.html.twig', [
            'items' => $items,
            'paginator' => $paginator,
            'filters' => $filterFormBuilder->getForm()->createView(),
            'entityType' => $entityType,
        ]);
    }

    /**
     * Serves dashboard.
     *
     * @Route("/dashboard/{entityType}", name="dashboard")
     *
     * @return mixed
     */
    public function dashboard(Request $request, $entityType)
    {
        $queryParameters = $request->query;
        $formParameters = array_merge([
            'groups' => '',
            'subowner' => '',
            'theme' => '',
            'category' => '',
            'search' => '',
        ], $queryParameters->get('form') ?: []);

        // Get the groups the user is added to.
        $userGroups = $this->getUser()->getGroups();
        $userGroupsThemesAndCategories = $this->getUserGroupsThemesAndCategories($userGroups->toArray());

        // Get a query for the entity type.
        $repository = $this->getRepository($entityType);
        $query = $repository->createQueryBuilder('e');
        $query = $this->applyFilters($query, $formParameters);

        // Get sub owners if a group has been selected.
        $subOwnerOptions = $this->getSubOwnerOptions($repository, $formParameters['groups']);

        $paginator = $this->paginator->paginate(
            $query,
            $queryParameters->get('page', 1),
            10
        );

        $items = $paginator->getItems();

        $availableCategories = $this->getAvailableCategories($items,
            $formParameters['theme'], $userGroupsThemesAndCategories['groups'],
            $formParameters['category']);

        $filterFormBuilder = $this->getFilterFormBuilder($userGroupsThemesAndCategories,
            $formParameters, $subOwnerOptions, true, true);
        $filterFormBuilder->setMethod('GET')
            ->setAction($this->generateUrl('dashboard',
                ['entityType' => $entityType]));

        return $this->render('dashboard.html.twig', [
            'paginator' => $paginator,
            'categories' => $availableCategories,
            'filters' => $filterFormBuilder->getForm()->createView(),
            'entityType' => $entityType,
        ]);
    }

    /**
     * Get subowners for selected group.
     *
     * @param $repository
     * @param $selectedGroups
     * @return mixed
     */
    private function getSubOwnerOptions($repository, $selectedGroups) {
        $groups = $this->entityManager->getRepository(Group::class)->findBy([
            'id' => $selectedGroups,
        ]);

        $subOwnersQueryBuilder = $repository->createQueryBuilder('e');
        $subOwnersQueryBuilder->select('DISTINCT e.sysOwnerSub');
        $subOwnersQueryBuilder->andWhere('e.sysOwnerSub IS NOT NULL');

        foreach ($groups as $group) {
            $subOwnersQueryBuilder->andWhere($subOwnersQueryBuilder->expr()->isMemberOf(':group'.$group->getId(), 'e.groups'));
            $subOwnersQueryBuilder->setParameter(':group'.$group->getId(), $group);
        }

        $subOwners = $subOwnersQueryBuilder->getQuery()->getResult();

        return array_reduce($subOwners,
            function ($carry, $item) {
                $carry[$item['sysOwnerSub']] = $item['sysOwnerSub'];

                return $carry;
            }, []);
    }

    /**
     * Get repository for entity type.
     *
     * @param $entityType
     * @return \App\Repository\ReportRepository|\App\Repository\SystemRepository|\Doctrine\Common\Persistence\ObjectRepository|null
     */
    private function getRepository($entityType)
    {
        switch ($entityType) {
            case 'system':
                return $this->entityManager->getRepository(System::class);
            case 'report':
                return $this->entityManager->getRepository(Report::class);
        }

        return null;
    }

    /**
     * Get filter form builder.
     *
     * @param $userGroupsThemesAndCategories
     * @param $formParameters
     * @param $subownerOptions
     * @param $entityType
     * @param bool $filterThemes
     * @param bool $filterCategories
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    private function getFilterFormBuilder(
        $userGroupsThemesAndCategories,
        $formParameters,
        $subownerOptions,
        bool $filterThemes = false,
        bool $filterCategories = false
    ) {
        $filterFormBuilder = $this->createFormBuilder();
        $filterFormBuilder->add('groups', ChoiceType::class, [
            'label' => 'filter.groups',
            'placeholder' => 'filter.placeholder.groups',
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'multiple' => true,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['groups']) ? $formParameters['groups'] : null,
        ]);
        $filterFormBuilder->add('subowner', ChoiceType::class, [
            'label' => 'filter.subowner',
            'placeholder' => 'filter.placeholder.subowner',
            'choices' => $subownerOptions,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'disabled' => count($subownerOptions) == 0,
            'data' => isset($formParameters['subowner']) ? $formParameters['subowner'] : null,
        ]);
        if ($filterThemes) {
            $filterFormBuilder->add('theme', ChoiceType::class, [
                'label' => 'filter.theme',
                'placeholder' => 'filter.placeholder.theme',
                'choices' => array_flip($userGroupsThemesAndCategories['themes']),
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'data' => isset($formParameters['theme']) ? $formParameters['theme'] : null,
            ]);
        }
        if ($filterCategories) {
            $filterFormBuilder->add('category', ChoiceType::class, [
                'label' => 'filter.category',
                'placeholder' => 'filter.placeholder.category',
                'choices' => array_flip($userGroupsThemesAndCategories['categories']),
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'data' => isset($formParameters['category']) ? $formParameters['category'] : null,
            ]);
        }
        $filterFormBuilder->add('search', TextType::class, [
            'label' => 'filter.search',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'filter.placeholder.search',
            ],
            'required' => false,
            'data' => isset($formParameters['search']) ? $formParameters['search'] : null,
        ]);

        return $filterFormBuilder;
    }

    /**
     * Get available categories.
     *
     * @param $items
     * @param $selectedTheme
     * @param $userGroups
     * @param $selectedCategory
     * @return array
     */
    private function getAvailableCategories(
        $items,
        $selectedTheme,
        $userGroups,
        $selectedCategory
    ) {
        $availableCategories = [];

        foreach ($items as $item) {
            /* @var Theme $theme */
            $theme = $item->getTheme();

            if ($theme == null) {
                continue;
            }

            if ($selectedTheme != '') {
                if ($theme->getId() != $selectedTheme) {
                    continue;
                }
            }

            $themeGroups = array_reduce($theme->getGroups()->toArray(),
                function ($carry, Group $item) {
                    $carry[$item->getId()] = $item->getName();

                    return $carry;
                }, []);

            $intersect = array_intersect($themeGroups, $userGroups);

            foreach ($theme->getOrderedCategories() as $category) {
                if ($selectedCategory != '') {
                    if ($category->getId() != $selectedCategory) {
                        continue;
                    }
                }

                if (count($intersect) > 0) {
                    $availableCategories[$category->getId()] = $category;
                }
            }
        }

        return $availableCategories;
    }

    /**
     * Apply common filters for report and system query.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param array $formParameters
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function applyFilters(
        QueryBuilder $query,
        array $formParameters
    ) {
        $query->andWhere('e.archivedAt IS NULL');

        // Get the groups the user can search in.
        if (!empty($formParameters['groups'])) {
            $groups = $this->entityManager->getRepository(Group::class)->findBy([
               'id' => $formParameters['groups']
            ]);

            foreach ($groups as $group) {
                $query->andWhere($query->expr()->isMemberOf(':group'.$group->getId(), 'e.groups'));
                $query->setParameter(':group'.$group->getId(), $group);
            }
        }

        if (isset($formParameters['search']) && $formParameters['search'] != '') {
            $query->andWhere('e.name LIKE :name');
            $query->setParameter('name', '%'.$formParameters['search'].'%');
        }

        if (isset($formParameters['subowner']) && $formParameters['subowner'] != '') {
            $query->andWhere('e.sysOwnerSub = :subowner');
            $query->setParameter('subowner', $formParameters['subowner']);
        }

        return $query;
    }

    /**
     * Get array of a user's groups, themes and categories.
     *
     * @param array $userGroups
     * @return mixed
     */
    private function getUserGroupsThemesAndCategories(array $userGroups)
    {
        return array_reduce($userGroups,
            function ($carry, Group $group) {
                $carry['groups'][$group->getId()] = $group->getName();

                foreach ($group->getThemes() as $theme) {
                    $carry['themes'][$theme->getId()] = $theme->getName();

                    foreach ($theme->getOrderedCategories() as $category) {
                        $carry['categories'][$category->getId()] = $category->getName();
                    }
                }

                return $carry;
            }, []);
    }
}
