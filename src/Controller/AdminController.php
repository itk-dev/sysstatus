<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Report;
use App\Entity\System;
use App\Entity\Theme;
use App\Repository\CategoryRepository;
use App\Repository\ThemeCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * Serves report and system lists.
     *
     * @Route("/list/{entityType}", name="list")
     *
     * @return mixed
     */
    public function list(Request $request, $entityType)
    {
        $queryParameters = $request->query;
        $formParameters = $queryParameters->get('form') ?: [
            'group' => '',
            'subowner' => '',
            'theme' => '',
            'category' => '',
            'search' => '',
        ];

        $repository = null;

        // Get query builder for entity type.
        switch ($entityType) {
            case 'system':
                $repository = $this->entityManager->getRepository(System::class);
                break;
            case 'report':
            default:
                $repository = $this->entityManager->getRepository(Report::class);
                break;
        }

        // Get a query for the entity type.
        $query = $repository->createQueryBuilder('e');
        $query->andWhere('e.archivedAt IS NULL');

        // Get the groups the user is added to.
        $userGroups = $this->getUser()->getGroups();

        $userGroupsThemesAndCategories = array_reduce($userGroups->toArray(),
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

        $groups = [];
        $subownerOptions = [];

        // Get the groups the user can search in.
        if (isset($formParameters['group']) && $formParameters['group'] != '') {
            if (isset($userGroupsThemesAndCategories['groups'][$formParameters['group']])) {
                $groups[] = $formParameters['group'];
            }

            // Get subowners if a group has been selected.
            $subownersQueryBuilder = $repository->createQueryBuilder('e');
            $subownersQueryBuilder->select('DISTINCT e.sysOwnerSub');
            $subownersQueryBuilder->andWhere('e.group = :group');
            $subownersQueryBuilder->setParameter('group',
                $formParameters['group']);
            $subowners = $subownersQueryBuilder->getQuery()->getResult();

            $subownerOptions = array_reduce($subowners,
                function ($carry, $item) {
                    $carry[$item['sysOwnerSub']] = $item['sysOwnerSub'];

                    return $carry;
                }, []);
        } else {
            $groups = $userGroups;
        }

        $query->andWhere('e.group IN (:groups)');
        $query->setParameter('groups', $groups);

        if (isset($formParameters['search']) && $formParameters['search'] != '') {
            $query->andWhere('e.name LIKE :name');
            $query->setParameter('name', '%'.$formParameters['search'].'%');
        }

        if (isset($formParameters['subowner']) && $formParameters['subowner'] != '') {
            $query->andWhere('e.sysOwnerSub = :subowner');
            $query->setParameter('subowner', $formParameters['subowner']);
        }

        $paginator = $this->paginator->paginate(
            $query,
            $queryParameters->get('page', 1),
            10
        );

        $filterFormBuilder = $this->createFormBuilder();
        $filterFormBuilder->add('group', ChoiceType::class, [
            'label' => 'filter.group',
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['group']) ? $formParameters['group'] : null,
        ]);
        $filterFormBuilder->add('subowner', ChoiceType::class, [
            'label' => 'filter.subowner',
            'choices' => $subownerOptions,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'disabled' => !isset($formParameters['group']) || $formParameters['group'] == '',
            'data' => isset($formParameters['subowner']) ? $formParameters['subowner'] : null,
        ]);
        $filterFormBuilder->add('search', TextType::class, [
            'label' => 'filter.search',
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['search']) ? $formParameters['search'] : null,
        ]);
        $filterFormBuilder->setMethod('GET')
            ->setAction($this->generateUrl('list',
                ['entityType' => $entityType]));

        switch ($entityType) {
            case 'system':
                $items = [];

                /* @var System $item */
                foreach ($paginator->getItems() as $item) {
                    $items[$item->getId()] = [
                        'entity.system.id' => $item->getId(),
                        'entity.system.sys_title' => $item->getSysTitle(),
                        'entity.system.group' => $item->getGroup() ? $item->getGroup()->getName() : '',
                        'entity.system.sys_owner_sub' => $item->getSysOwnerSub(),
                        'entity.system.sys_system_owner' => $item->getSysSystemOwner(),
                        'entity.system.sys_link' => '<a href="'.$item->getSysLink().'">Link</a>',
                        'entity.system.theme' => $item->getTheme() ? $item->getTheme()->getName() : '',
                        'entity.system.selfServiceAvailableFromItems' => array_reduce($item->getSelfServiceAvailableFromItems()->toArray(), function ($carry, $item) {
                            $carry = (strlen($carry) > 0) ? $carry.', '.$item : $item;
                            return $carry;
                        }, ''),
                        'entity.system.text' => $item->getTextSet() ? '<label class="label label-success">Ja</label>' : '',
                    ];
                }
                break;
            case 'report':
            default:
                $items = [];

                /* @var Report $item */
                foreach ($paginator->getItems() as $item) {
                    $items[$item->getId()] = [
                        'entity.report.id' => $item->getId(),
                        'entity.report.sys_title' => $item->getSysTitle(),
                        'entity.report.group' => $item->getGroup() ? $item->getGroup()->getName() : '',
                        'entity.report.sys_owner_sub' => $item->getSysOwnerSub(),
                        'entity.report.sys_system_owner' => $item->getSysSystemOwner(),
                        'entity.report.sys_link' => '<a href="'.$item->getSysLink().'">Link</a>',
                        'entity.report.theme' => $item->getTheme() ? $item->getTheme()->getName() : '',
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
        $formParameters = $queryParameters->get('form') ?: [
            'group' => '',
            'subowner' => '',
            'theme' => '',
            'category' => '',
            'search' => '',
        ];

        $repository = null;

        // Get query builder for entity type.
        switch ($entityType) {
            case 'system':
                $repository = $this->entityManager->getRepository(System::class);
                break;
            case 'report':
            default:
                $repository = $this->entityManager->getRepository(Report::class);
                break;
        }

        // Get a query for the entity type.
        $query = $repository->createQueryBuilder('e');
        // @TODO: Add this to ignore archived entries.
        //$query->andWhere('e.archivedAt IS NULL');

        // Get the groups the user is added to.
        $userGroups = $this->getUser()->getGroups();

        $userGroupsThemesAndCategories = array_reduce($userGroups->toArray(),
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

        $groups = [];
        $subownerOptions = [];

        // Get the groups the user can search in.
        if (isset($formParameters['group']) && $formParameters['group'] != '') {
            if (isset($userGroupsThemesAndCategories['groups'][$formParameters['group']])) {
                $groups[] = $formParameters['group'];
            }

            // Get subowners if a group has been selected.
            $subownersQueryBuilder = $repository->createQueryBuilder('e');
            $subownersQueryBuilder->select('DISTINCT e.sysOwnerSub');
            $subownersQueryBuilder->andWhere('e.group = :group');
            $subownersQueryBuilder->setParameter('group',
                $formParameters['group']);
            $subowners = $subownersQueryBuilder->getQuery()->getResult();

            $subownerOptions = array_reduce($subowners,
                function ($carry, $item) {
                    $carry[$item['sysOwnerSub']] = $item['sysOwnerSub'];

                    return $carry;
                }, []);
        } else {
            $groups = $userGroups;
        }

        $query->andWhere('e.group IN (:groups)');
        $query->setParameter('groups', $groups);

        if (isset($formParameters['search']) && $formParameters['search'] != '') {
            $query->andWhere('e.name LIKE :name');
            $query->setParameter('name', '%'.$formParameters['search'].'%');
        }

        if (isset($formParameters['subowner']) && $formParameters['subowner'] != '') {
            $query->andWhere('e.sysOwnerSub = :subowner');
            $query->setParameter('subowner', $formParameters['subowner']);
        }

        $paginator = $this->paginator->paginate(
            $query,
            $queryParameters->get('page', 1),
            10
        );

        $items = $paginator->getItems();

        $availableCategories = [];

        foreach ($items as $item) {
            /* @var Theme $theme */
            $theme = $item->getTheme();

            if ($theme == null) {
                continue;
            }

            if ($formParameters['theme'] != '') {
                if ($theme->getId() != $formParameters['theme']) {
                    continue;
                }
            }

            $themeGroups = array_reduce($theme->getGroups()->toArray(),
                function ($carry, Group $item) {
                    $carry[$item->getId()] = $item->getName();

                    return $carry;
                }, []);

            $intersect = array_intersect($themeGroups,
                $userGroupsThemesAndCategories['groups']);

            foreach ($theme->getOrderedCategories() as $category) {
                if ($formParameters['category'] != '') {
                    if ($category->getId() != $formParameters['category']) {
                        continue;
                    }
                }

                if (count($intersect) > 0) {
                    $availableCategories[$category->getId()] = $category;
                }
            }
        }

        $filterFormBuilder = $this->createFormBuilder();
        $filterFormBuilder->add('group', ChoiceType::class, [
            'label' => 'filter.group',
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['group']) ? $formParameters['group'] : null,
        ]);
        $filterFormBuilder->add('subowner', ChoiceType::class, [
            'label' => 'filter.subowner',
            'choices' => $subownerOptions,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'disabled' => !isset($formParameters['group']) || $formParameters['group'] == '',
            'data' => isset($formParameters['subowner']) ?  : null,
        ]);
        $filterFormBuilder->add('theme', ChoiceType::class, [
            'label' => 'filter.theme',
            'choices' => array_flip($userGroupsThemesAndCategories['themes']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['theme']) ? $formParameters['theme'] : null,
        ]);
        $filterFormBuilder->add('category', ChoiceType::class, [
            'label' => 'filter.category',
            'choices' => array_flip($userGroupsThemesAndCategories['categories']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['category']) ? $formParameters['category'] : null,
        ]);
        $filterFormBuilder->add('search', TextType::class, [
            'label' => 'filter.search',
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['search']) ? $formParameters['search'] : null,
        ]);
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
}
