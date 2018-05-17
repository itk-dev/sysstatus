<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminController extends BaseAdminController
{

    /**
     * The method that is executed when the user performs a 'show' action on an entity.
     *
     * @return Response
     */
    protected function showAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_SHOW);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        $fields = $this->entity['show']['fields'];
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $this->dispatch(EasyAdminEvents::POST_SHOW, array(
            'deleteForm' => $deleteForm,
            'fields' => $fields,
            'entity' => $entity,
        ));

        $parameters = array(
            'entity' => $entity,
            'fields' => $fields,
            'delete_form' => $deleteForm->createView(),
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('show', 'easy_admin_overrides/show.html.twig', $parameters));
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
            'easy_admin_overrides/list.html.twig',
            array(
                'paginator' => $paginator,
                'fields' => $fields,
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
            )
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
            $queryParameters = array_replace($this->request->query->all(), array('action' => 'list', 'query' => null));
            $queryParameters = array_filter($queryParameters);

            return $this->redirect($this->get('router')->generate('easyadmin', $queryParameters));
        }

        $searchableFields = $this->entity['search']['fields'];
        $paginator = $this->findBy(
            $this->entity['class'],
            $query,
            $searchableFields,
            $this->request->query->get('page', 1),
            $this->entity['list']['max_results'],
            isset($this->entity['search']['sort']['field']) ? $this->entity['search']['sort']['field'] : $this->request->query->get('sortField'),
            isset($this->entity['search']['sort']['direction']) ? $this->entity['search']['sort']['direction'] : $this->request->query->get('sortDirection'),
            $this->entity['search']['dql_filter']
        );
        $fields = $this->entity['list']['fields'];

        $this->dispatch(EasyAdminEvents::POST_SEARCH, array(
            'fields' => $fields,
            'paginator' => $paginator,
        ));

        $parameters = array(
            'paginator' => $paginator,
            'fields' => $fields,
            'delete_form_template' => $this->createDeleteForm($this->entity['name'], '__id__')->createView(),
            'filters' => isset($this->entity['list']['filters']) ? $this->createFilterForm(
                    $this->entity['list']['filters'],
                    $this->request->query->get('filters', []),
                    $this->generateUrl(
                        'filter',
                        $this->request->query->all()
                    )
                )->createView() : null,
        );

        return $this->executeDynamicMethod('render<EntityName>Template', array('search', 'easy_admin_overrides/list.html.twig', $parameters));
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
                                'class' => 'form-control',
                            ),
                        )
                    );
                    break;
                case 'choice':
                    $formBuilder->add(
                        $filter['property'],
                        ChoiceType::class,
                        array(
                            'label' => false,
                            'translation_domain' => 'messages',
                            'required' => false,
                            'placeholder' => $filter['property'],
                            'data' => $requestFilters[$filter['property']] ?? null,
                            'choices' => $filter['choices'],
                            'attr' => array(
                                'class' => 'form-control',
                            ),
                        )
                    );
                    break;
            }
        }

        $formBuilder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Apply',
            ]
        );

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

        $params['filters'] = null;

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
     * @param $name
     * @return null
     */
    public function getClassByName($name)
    {
        $backendConfig = $this->get('easyadmin.config.manager')->getBackendConfig();
        foreach ($backendConfig['entities'] as $entityName => $entityConfig) {
            if ($entityName === $name) {
                return $entityConfig;
            }
        }

        return null;
    }
}
