<?php

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\AdminController as BaseAdminController;
use App\Entity\Category;
use App\Entity\Group;
use App\Entity\Report;
use App\Entity\System;
use App\Entity\Theme;
use App\Repository\CategoryRepository;
use App\Repository\SystemRepository;
use App\Repository\ThemeCategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminController extends BaseAdminController
{
    private $categoryRepository;
    private $themeCategoryRepository;
    private $entityManager;
    private $paginator;

    /**
     * AdminController constructor.
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
        $formParameters = $queryParameters->get('form');

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
        $formParameters = $queryParameters->get('form');

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
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['group']) ? $formParameters['group'] : null,
        ]);
        $filterFormBuilder->add('subowner', ChoiceType::class, [
            'choices' => $subownerOptions,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'disabled' => !isset($formParameters['group']) || $formParameters['group'] == '',
            'data' => isset($formParameters['subowner']) ? $formParameters['subowner'] : null,
        ]);
        $filterFormBuilder->add('theme', ChoiceType::class, [
            'choices' => array_flip($userGroupsThemesAndCategories['themes']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['theme']) ? $formParameters['theme'] : null,
        ]);
        $filterFormBuilder->add('category', ChoiceType::class, [
            'choices' => array_flip($userGroupsThemesAndCategories['categories']),
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['category']) ? $formParameters['category'] : null,
        ]);
        $filterFormBuilder->add('search', TextType::class, [
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'data' => isset($formParameters['search']) ? $formParameters['search'] : null,
        ]);
        $filterFormBuilder->setMethod('GET')
            ->setAction($this->generateUrl('list',
                ['entityType' => $entityType]));

        return $this->render('dashboard.html.twig', [
            'paginator' => $paginator,
            'categories' => $availableCategories,
            'filters' => $filterFormBuilder->getForm()->createView(),
            'entityType' => $entityType,
        ]);
    }

    /**
     * The method that is executed when the user performs a 'list' action on an entity.
     *
     * Modified version of: https://github.com/alterphp/EasyAdminExtensionBundle/issues/29
     *
     * @return Response
     */
    protected function listAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $fields = $this->entity['list']['fields'];
        $paginator = $this->findAll(
            $this->entity['class'],
            $this->request->query->get('page', 1),
            $this->config['list']['max_results'],
            $this->request->query->get('sortField'),
            $this->request->query->get('sortDirection'),
            $this->entity['list']['dql_filter']
        );

        $this->dispatch(
            EasyAdminEvents::POST_LIST,
            ['paginator' => $paginator]
        );

        return $this->render(
            $this->entity['templates']['list'],
            [
                'paginator' => $paginator,
                'fields' => $fields,
                'icon' => $this->getIconForEntity($this->entity['name']),
                'delete_form_template' => $this->createDeleteForm(
                    $this->entity['name'],
                    '__id__'
                )->createView(),
                'filters' => isset($this->entity['list']['filters']) ? $this->createFilterForm(
                    $this->entity['list']['filters'],
                    $this->request->query->get('filters', []),
                    $this->generateUrl(
                        'filter',
                        $this->request->query->all()
                    )
                )->createView() : null,
            ]
        );
    }

    /**
     * The method that is executed when the user performs a query on an entity.
     *
     * @return Response
     */
    protected function searchAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SEARCH);

        $query = trim($this->request->query->get('query'));
        // if the search query is empty, redirect to the 'list' action
        if ('' === $query) {
            $queryParameters = array_replace(
                $this->request->query->all(),
                ['action' => 'list', 'query' => null]
            );
            $queryParameters = array_filter($queryParameters);

            return $this->redirect(
                $this->get('router')->generate('easyadmin', $queryParameters)
            );
        }

        $searchableFields = $this->entity['search']['fields'];
        $paginator = $this->findBy(
            $this->entity['class'],
            $query,
            $searchableFields,
            $this->request->query->get('page', 1),
            $this->entity['list']['max_results'],
            isset($this->entity['search']['sort']['field']) ? $this->entity['search']['sort']['field'] : $this->request->query->get(
                'sortField'
            ),
            isset($this->entity['search']['sort']['direction']) ? $this->entity['search']['sort']['direction'] : $this->request->query->get(
                'sortDirection'
            ),
            $this->entity['search']['dql_filter']
        );
        $fields = $this->entity['list']['fields'];

        $this->dispatch(
            EasyAdminEvents::POST_SEARCH,
            [
                'fields' => $fields,
                'paginator' => $paginator,
            ]
        );

        $parameters = [
            'paginator' => $paginator,
            'fields' => $fields,
            'icon' => $this->getIconForEntity($this->entity['name']),
            'delete_form_template' => $this->createDeleteForm(
                $this->entity['name'],
                '__id__'
            )->createView(),
            'filters' => isset($this->entity['list']['filters']) ? $this->createFilterForm(
                $this->entity['list']['filters'],
                $this->request->query->get('filters', []),
                $this->generateUrl(
                    'filter',
                    $this->request->query->all()
                )
            )->createView() : null,
        ];

        return $this->executeDynamicMethod(
            'render<EntityName>Template',
            ['search', $this->entity['templates']['list'], $parameters]
        );
    }

    /**
     * Create filter form
     *
     * Modified version of: https://github.com/alterphp/EasyAdminExtensionBundle/issues/29
     *
     * @param $filters
     * @param $requestFilters
     * @param $route
     * @return null
     */
    public function createFilterForm($filters, $requestFilters, $route)
    {
        if (!$filters) {
            return null;
        }
        /** @var FormBuilder $formBuilder */
        $formBuilder = $this->get('form.factory')
            ->createNamedBuilder('filter')
            ->setMethod('POST')
            ->setAction($route);

        foreach ($filters as $filter) {
            if (!isset($filter['type'])) {
                $filter['type'] = 'entity';
            }

            switch ($filter['type']) {
                case 'entity':
                    $entityConfig = $this->getClassByName(
                        $filter['class'] ?? ucfirst($filter['property'])
                    );
                    if (null === $entityConfig && $filter['class']) {
                        $entityConfig = ['class' => $filter['class']];
                    }
                    $selected = null;
                    if (isset($requestFilters[$filter['property']])) {
                        $selected = $this->getDoctrine()
                            ->getManagerForClass($entityConfig['class'])
                            ->find(
                                $entityConfig['class'],
                                $requestFilters[$filter['property']]
                            );
                    }
                    $formBuilder->add(
                        $filter['property'],
                        EntityType::class,
                        [
                            'label' => false,
                            'class' => $entityConfig['class'],
                            'translation_domain' => 'messages',
                            'required' => false,
                            'placeholder' => $filter['placeholder'] ?? 'custom_filters.none',
                            'data' => $selected,
                            'attr' => [
                                'class' => 'form-control custom-filter-select',
                            ],
                        ]
                    );
                    break;
                case 'choice':
                    $disableFilter = false;

                    if (isset($filter['extract_from_property']) &&
                        isset($filter['parent_filter']) &&
                        (!isset($requestFilters[$filter['parent_filter']]) || $requestFilters[$filter['parent_filter']] == '')) {
                        $disableFilter = true;
                    }

                    if (isset($filter['extract_from_property']) && !isset($filter['choices'])) {
                        $builder = $this->entityManager->getRepository($this->entity['class'])
                            ->createQueryBuilder('en');

                        $field = 'en.'.$filter['extract_from_property'];

                        $builder
                            ->select($field)
                            ->distinct($field);

                        if (isset($filter['parent_filter']) && !empty($requestFilters[$filter['parent_filter']])) {
                            $builder
                                ->andWhere('en.'.$filter['parent_filter'].' = :parent_filter')
                                ->setParameter('parent_filter',
                                    $requestFilters[$filter['parent_filter']]);
                        }

                        $query = $builder->getQuery();

                        $results = $query->getResult();

                        $choices = [($filter['placeholder'] ?? 'All') => ''];

                        foreach ($results as $result) {
                            if ($result[$filter['extract_from_property']]) {
                                $choices[$result[$filter['extract_from_property']]] = $result[$filter['extract_from_property']];
                            }
                        }

                        $filter['choices'] = $choices;
                    }

                    $renderArray = [
                        'label' => false,
                        'translation_domain' => 'messages',
                        'required' => false,
                        'placeholder' => $filter['placeholder'] ?? null,
                        'data' => $requestFilters[$filter['property']] ?? null,
                        'choices' => $filter['choices'],
                        'attr' => [
                            'class' => 'form-control custom-filter-select',
                        ],
                    ];

                    if ($disableFilter) {
                        $renderArray['attr']['disabled'] = 'disabled';
                    }

                    $formBuilder->add(
                        $filter['property'],
                        ChoiceType::class,
                        $renderArray
                    );
                    break;
            }
        }

        $form = $formBuilder->getForm();

        return $form;
    }

    /**
     * Filter.
     *
     * Modified version of: https://github.com/alterphp/EasyAdminExtensionBundle/issues/29
     *
     * @Route("/filter", name="filter")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function filterAction(Request $request)
    {
        $filters = ['filters' => $request->get('filter')];

        unset($filters['filters']['_token']);
        unset($filters['filters']['submit']);

        $params = $request->query->all();

        // Change in group filter, resets all other filters.
        if (isset($params['filters']) && isset($filters['filters']) &&
            $params['filters']['group'] != $filters['filters']['group']) {
            foreach ($filters['filters'] as $key => $filter) {
                if ($key != 'group') {
                    unset($filters['filters'][$key]);
                }
            }
        }

        $params['filters'] = null;
        $params['page'] = 1;

        return $this->redirectToRoute(
            'easyadmin',
            array_merge(
                $params,
                $filters
            )
        );
    }

    /**
     * Get class from name.
     *
     * From: https://github.com/alterphp/EasyAdminExtensionBundle/issues/29
     *
     * @param $name
     * @return null
     */
    public function getClassByName($name)
    {
        $backendConfig = $this->get('easyadmin.config.manager')
            ->getBackendConfig();
        foreach ($backendConfig['entities'] as $entityName => $entityConfig) {
            if ($entityName === $name) {
                return $entityConfig;
            }
        }

        return null;
    }

    /**
     * Get font-awesome icon for $entity
     *
     * @param $entity
     * @return string
     */
    private function getIconForEntity($entity)
    {
        switch ($entity) {
            case 'Report':
                return 'file';
            case 'System':
                return 'cogs';
            case 'Note':
                return 'edit';
            case 'User':
                return 'user';
            case 'Group':
                return 'users';
            case 'Theme':
                return 'th-large';
            case 'ThemeCategory':
                return 'arrows-v';
            case 'Category':
                return 'list';
            case 'Question':
                return 'question';
            default:
                return 'chevron-circle-right';
        }
    }
}
