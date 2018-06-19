<?php

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\AdminController as BaseAdminController;
use App\Repository\CategoryRepository;
use App\Repository\SystemRepository;
use App\Repository\ThemeCategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * AdminController constructor.
     */
    public function __construct(CategoryRepository $categoryRepository, ThemeCategoryRepository $themeCategoryRepository, EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->themeCategoryRepository = $themeCategoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Serves dashboard.
     *
     * @return mixed
     */
    protected function dashboardAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $query = trim($this->request->query->get('query'));

        $paginator = null;

        $maxNumberOfResults = 15;

        if ('' === $query) {
            $paginator = $this->findAll(
                $this->entity['class'],
                $this->request->query->get('page', 1),
                $maxNumberOfResults,
                'id',
                'ASC',
                $this->entity['list']['dql_filter']
            );
        } else {
            $this->entity['search']['fields'] = ['name' => $this->entity['search']['fields']['name']];

            $searchableFields = $this->entity['search']['fields'];

            $paginator = $this->findBy(
                $this->entity['class'],
                $query,
                $searchableFields,
                $this->request->query->get('page', 1),
                $maxNumberOfResults,
                isset($this->entity['search']['sort']['field']) ? $this->entity['search']['sort']['field'] : $this->request->query->get(
                    'sortField'
                ),
                'ASC',
                $this->entity['search']['dql_filter']
            );
        }
        $fields = $this->entity['list']['fields'];

        $this->dispatch(
            EasyAdminEvents::POST_LIST,
            array(
                'fields' => $fields,
                'paginator' => $paginator,
            )
        );

        $it = $paginator->getCurrentPageResults();

        $themes = [];
        $categories = [];

        while ($it->valid()) {
            $entity = $it->current();

            $theme = $entity->getTheme();

            if (isset($theme) && !array_key_exists($theme->getId(), $themes)) {
                $themes[$theme->getId()] = $theme;

                foreach ($theme->getOrderedCategories() as $category) {
                  $categories[$category->getId()] = $category;
                }
            }

            $it->next();
        }

        $parameters = array(
            'paginator' => $paginator,
            'fields' => $fields,
            'icon' => $this->getIconForEntity($this->entity['name']),
            'themes' => $themes,
            'categories' => $categories,
            'filters' => isset($this->entity['list']['filters']) ? $this->createFilterForm(
                $this->entity['list']['filters'],
                $this->request->query->get('filters', []),
                $this->generateUrl(
                    'filter',
                    $this->request->query->all()
                )
            )->createView() : null,
        );

        return $this->executeDynamicMethod(
            'render<EntityName>Template',
            array(
                'dashboard',
                'easy_admin_overrides/dashboard.html.twig',
                $parameters,
            )
        );
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
            array('paginator' => $paginator)
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
                array('action' => 'list', 'query' => null)
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
            array(
                'fields' => $fields,
                'paginator' => $paginator,
            )
        );

        $parameters = array(
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
        );

        return $this->executeDynamicMethod(
            'render<EntityName>Template',
            array('search', $this->entity['templates']['list'], $parameters)
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
                        array(
                            'label' => false,
                            'class' => $entityConfig['class'],
                            'translation_domain' => 'messages',
                            'required' => false,
                            'placeholder' => 'custom_filters.none',
                            'data' => $selected,
                            'attr' => array(
                                'class' => 'form-control custom-filter-select',
                            ),
                        )
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
                        $query = "SELECT DISTINCT u.".$filter['extract_from_property']." FROM ".$this->entity['class']." u";
                        if (isset($filter['parent_filter']) && !empty($requestFilters[$filter['parent_filter']])) {
                            $query .= ' WHERE u.' . $filter['parent_filter'] . ' = ' . $requestFilters[$filter['parent_filter']];
                        }

                        $query = $this->entityManager->createQuery($query);
                        $results =  $query->getResult();

                        $choices = [];
                        $choices['All'] = '';

                        foreach ($results as $result) {
                            $choices[$result['sysOwnerSub']] = $result['sysOwnerSub'];
                        }

                        $filter['choices'] = $choices;
                    }

                    $renderArray = [
                        'label' => false,
                        'translation_domain' => 'messages',
                        'required' => false,
                        'placeholder' => null,
                        'data' => $requestFilters[$filter['property']] ?? null,
                        'choices' => $filter['choices'],
                        'attr' => array(
                            'class' => 'form-control custom-filter-select',
                        ),
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
            foreach($filters['filters'] as $key => $filter) {
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
    private function getIconForEntity($entity) {
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
