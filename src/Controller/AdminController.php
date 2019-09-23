<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Group;
use App\Entity\Question;
use App\Entity\Report;
use App\Entity\SelfServiceAvailableFromItem;
use App\Entity\System;
use App\Entity\Theme;
use App\Entity\ThemeCategory;
use App\Repository\CategoryRepository;
use App\Repository\ThemeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminController extends EasyAdminController
{
    private $categoryRepository;
    private $themeCategoryRepository;
    private $entityManager;
    private $paginator;
    private $translator;

    /**
     * AdminController constructor.
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @param \App\Repository\ThemeCategoryRepository $themeCategoryRepository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ThemeCategoryRepository $themeCategoryRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        TranslatorInterface $translator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->themeCategoryRepository = $themeCategoryRepository;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->translator = $translator;
    }

    /**
     * Overrides EasyAdmin delete action.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction()
    {
        $entityArray = $this->entity;
        switch ($entityArray['class']) {
            case Report::class:
            case System::class:
            case Answer::class:
            case Theme::class:
            case ThemeCategory::class:
            case Category::class:
            case Question::class:
                $entity = $this->getEntity($entityArray['class'], $_GET['id']);
                $accessGranted = $this->isGranted('delete', $entity);

                if (!$accessGranted) {
                    $this->addFlash('danger', $this->translator->trans('flash.access_denied'));
                    return $this->redirectToReferrer();
                }
                break;
        }

        return parent::deleteAction();
    }

    /**
     * Overrides EasyAdmin edit action.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction()
    {
        $entityArray = $this->entity;
        switch ($entityArray['class']) {
            case Report::class:
            case System::class:
            case Answer::class:
            case Theme::class:
            case ThemeCategory::class:
            case Category::class:
            case Question::class:
                $entity = $this->getEntity($entityArray['class'], $_GET['id']);
                $accessGranted = $this->isGranted('edit', $entity);

                if (!$accessGranted) {
                    $this->addFlash('danger', $this->translator->trans('flash.access_denied'));
                    return $this->redirectToReferrer();
                }
                break;
        }


        return parent::editAction();
    }

    /**
     * Overrides EasyAdmin show action.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $entityArray = $this->entity;
        if ($entityArray['class'] == Report::class || $entityArray['class'] == System::class) {
            $entity = $this->getEntity($entityArray['class'], $_GET['id']);
            $accessGranted = $this->isGranted('show', $entity);

            if (!$accessGranted) {
                $this->addFlash('error', $this->translator->trans('flash.access_denied'));
                return $this->redirectToRoute('list', ['entityType' => strtolower($entityArray['name'])]);
            }
        }

        return parent::showAction();
    }

    /**
     * Overrides EasyAdmin list action.
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

        $userGroups = $this->entityManager->getRepository(Group::class)->findAll();
        $userGroupsThemesAndCategories = $this->getUserGroupsThemesAndCategories($userGroups ?: [], $entityType);

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

        if ($entityType == 'system') {
            $selfServiceOptions = $this->entityManager->getRepository(SelfServiceAvailableFromItem::class)->findAll();
            $userGroupsThemesAndCategories['self_service'] = array_reduce($selfServiceOptions, function ($carry, SelfServiceAvailableFromItem $item) {
                $carry[$item->getId()] = $item->getName();
                return $carry;
            }, []);
        }

        $filterFormBuilder = $this->getFilterFormBuilder($userGroupsThemesAndCategories,
            $formParameters, $subOwnerOptions, false, false, $entityType == 'system');
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
                        'entity' => $item,
                        'permission' => [
                            'show' => $this->isGranted('show', $item),
                            'edit' => $this->isGranted('edit', $item),
                        ],
                        'fields' => [
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
                        ],
                    ];
                }
                break;
            case 'report':
                /* @var Report $item */
                foreach ($paginator->getItems() as $item) {
                    $items[$item->getId()] = [
                        'entity' => $item,
                        'permission' => [
                            'show' => $this->isGranted('show', $item),
                            'edit' => $this->isGranted('edit', $item),
                        ],
                        'fields' => [
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
                        ]
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
            'groups' => [],
            'subowner' => '',
            'theme' => '',
            'category' => '',
            'search' => '',
        ], $queryParameters->get('form') ?: []);

        $userGroups = $this->entityManager->getRepository(Group::class)->findAll();
        $userGroupsThemesAndCategories = $this->getUserGroupsThemesAndCategories($userGroups ?: [], $entityType);

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

        $availableCategories = [];
        $themes = [];

        if (!empty($formParameters['groups'])) {
            $groups = $this->entityManager->getRepository(Group::class)->findBy([
                'id' => $formParameters['groups']
            ]);
        }
        else {
            $groups = $userGroups;
        }

        foreach ($groups as $group) {
            $groupThemes = $entityType == 'report' ? $group->getReportThemes() : $group->getSystemThemes();

            foreach ($groupThemes as $theme) {
                if ($formParameters['theme'] != '') {
                    if ($theme->getId() != $formParameters['theme']) {
                        continue;
                    }
                }

                if (!isset($themes[$theme->getId()])) {
                    $themes[$theme->getId()] = $theme;

                    foreach ($theme->getOrderedCategories() as $category) {
                        if ($formParameters['category'] != '') {
                            if ($category->getId() != $formParameters['category']) {
                                continue;
                            }
                        }

                        $availableCategories[$category->getId()] = $category;
                    }
                }
            }
        }

        $filterFormBuilder = $this->getFilterFormBuilder($userGroupsThemesAndCategories, $formParameters, $subOwnerOptions, true, true);
        $filterFormBuilder->setMethod('GET')->setAction($this->generateUrl('dashboard', ['entityType' => $entityType]));

        return $this->render('dashboard.html.twig', [
            'paginator' => $paginator,
            'categories' => $availableCategories,
            'filters' => $filterFormBuilder->getForm()->createView(),
            'entityType' => $entityType,
        ]);
    }

    /**
     * @param $className
     * @param $id
     * @return object|null
     */
    private function getEntity($className, $id)
    {
        return $this->entityManager->getRepository($className)->find($id);
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
     * @param bool $filterThemes
     * @param bool $filterCategories
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    private function getFilterFormBuilder(
        $userGroupsThemesAndCategories,
        $formParameters,
        $subownerOptions,
        bool $filterThemes = false,
        bool $filterCategories = false,
        bool $filterSelfService = false
    ) {
        $filterFormBuilder = $this->createFormBuilder();
        $filterFormBuilder->add('groups', ChoiceType::class, [
            'label' => 'filter.groups',
            'placeholder' => 'filter.placeholder.groups',
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'multiple' => true,
            'attr' => [
                'class' => 'form-control',
                'data-placeholder' => $this->translator->trans('filter.placeholder.groups'),
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
        /* @TODO: Add self service filter.
        if ($filterSelfService) {
            $filterFormBuilder->add('self_service', ChoiceType::class, [
                'label' => 'filter.self_service',
                'placeholder' => 'filter.placeholder.self_service',
                'choices' => array_flip($userGroupsThemesAndCategories['self_service']),
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'data' => isset($formParameters['self_service']) ? $formParameters['self_service'] : null,
            ]);
        }*/
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
        // @TODO: Filter inactive out.

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

        // @TODO: Add self-service filter.

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
    private function getUserGroupsThemesAndCategories(array $userGroups, $entityType)
    {
        return array_reduce($userGroups,
            function ($carry, Group $group) use ($entityType) {
                $carry['groups'][$group->getId()] = $group->getName();

                $groupThemes = $entityType == 'report' ? $group->getReportThemes() : $group->getSystemThemes();

                foreach ($groupThemes as $theme) {
                    $carry['themes'][$theme->getId()] = $theme->getName();

                    foreach ($theme->getOrderedCategories() as $category) {
                        $carry['categories'][$category->getId()] = $category->getName();
                    }
                }

                return $carry;
            }, [
                'groups' => [],
                'themes' => [],
                'categories' => [],
            ]);
    }
}
