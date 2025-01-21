<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use App\Entity\SelfServiceAvailableFromItem;
use App\Entity\System;
use App\Entity\UserGroup;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomDashboardCrudController extends AbstractSystatusDashboardController
{
    /**
     * AdminController constructor.
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PaginatorInterface $paginator,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Redirect /admin to the reports page to hide easyAdmin's default front page.
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('dashboard', ['entityType' => 'report']);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: 'admin/{entityType}', name: 'dashboard')]
    public function dashboard(Request $request, string $entityType): Response
    {
        $queryParameters = $request->query;

        // Fetch all query parameters as an array.
        $queryParams = $request->query->all();

        // Safely retrieve 'form' as an array.
        $formData = $queryParams['form'] ?? [];

        // Ensure `formData` is always an array.
        if (!is_array($formData)) {
            $formData = [];
        }

        $formParameters = array_merge(
            [
                'groups' => [],
                'subowner' => '',
                'theme' => '',
                'category' => '',
                'self_service' => '',
                'search' => '',
            ],
            $formData
        );

        $userGroups = $this->entityManager
            ->getRepository(UserGroup::class)
            ->findAll();
        $userGroupsThemesAndCategories = $this->getUserGroupsThemesAndCategories(
            $userGroups ?: [],
            $entityType
        );

        // Get a query for the entity type.
        $repository = $this->getRepository($entityType);
        $query = $repository->createQueryBuilder('e');
        $query = $this->applyFilters($query, $formParameters, $entityType);

        // Get sub owners if a group has been selected.
        $subOwnerOptions = $this->getSubOwnerOptions(
            $repository,
            $formParameters['groups']
        );

        $paginator = $this->paginator->paginate(
            $query,
            $queryParameters->get('page', 1),
            10
        );

        $availableCategories = [];
        $themes = [];

        if (!empty($formParameters['groups'])) {
            $groups = $this->entityManager
                ->getRepository(UserGroup::class)
                ->findBy([
                    'id' => $formParameters['groups'],
                ]);
        } else {
            $groups = $userGroups;
        }

        foreach ($groups as $group) {
            $groupThemes =
                'report' == $entityType
                    ? $group->getReportThemes()
                    : $group->getSystemThemes();

            foreach ($groupThemes as $theme) {
                if ('' != $formParameters['theme']) {
                    if ($theme->getId() != $formParameters['theme']) {
                        continue;
                    }
                }

                if (!isset($themes[$theme->getId()])) {
                    $themes[$theme->getId()] = $theme;

                    foreach ($theme->getOrderedCategories() as $category) {
                        if ('' != $formParameters['category']) {
                            if (
                                $category->getId() !=
                                $formParameters['category']
                            ) {
                                continue;
                            }
                        }

                        $availableCategories[$category->getId()] = $category;
                    }
                }
            }
        }

        $selfServiceOptions = $this->getSelfServiceOptions($entityType);

        $filterFormBuilder = $this->getFilterFormBuilder(
            $userGroupsThemesAndCategories,
            $formParameters,
            $subOwnerOptions,
            true,
            true,
            $selfServiceOptions
        );
        $filterFormBuilder
            ->setMethod('GET')
            ->setAction(
                $this->generateUrl('dashboard', ['entityType' => $entityType])
            );

        return $this->render('dashboard.html.twig', [
            'paginator' => $paginator,
            'categories' => $availableCategories,
            'filters' => $filterFormBuilder->getForm()->createView(),
            'entityType' => $entityType,
        ]);
    }

    /**
     * @param array<UserGroup> $userGroups
     * @param string $entityType
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function getUserGroupsThemesAndCategories(
        array $userGroups,
        string $entityType,
    ): mixed {
        return array_reduce(
            $userGroups,
            function ($carry, UserGroup $group) use ($entityType) {
                $carry['groups'][$group->getId()] = $group->getName();

                $groupThemes =
                    'report' == $entityType
                        ? $group->getReportThemes()
                        : $group->getSystemThemes();

                foreach ($groupThemes as $theme) {
                    $carry['themes'][$theme->getId()] = $theme->getName();

                    foreach ($theme->getOrderedCategories() as $category) {
                        $carry['categories'][
                            $category->getId()
                        ] = $category->getName();
                    }
                }

                return $carry;
            },
            [
                'groups' => [],
                'themes' => [],
                'categories' => [],
            ]
        );
    }

    /**
     * Get repository for entity type.
     *
     * @param string $entityType
     *
     * @return ReportRepository|SystemRepository|null
     */
    private function getRepository(
        string $entityType,
    ): ReportRepository|SystemRepository|null {
        return match ($entityType) {
            'system' => $this->entityManager->getRepository(System::class),
            'report' => $this->entityManager->getRepository(Report::class),
            default => null,
        };
    }

    /**
     * Apply common filters for report and system query.
     *
     * @param QueryBuilder $query
     * @param array<string, mixed> $formParameters
     * @param string|null $entityType
     *
     * @return QueryBuilder
     */
    private function applyFilters(
        QueryBuilder $query,
        array $formParameters,
        ?string $entityType = null,
    ): QueryBuilder {
        $query->andWhere('e.archivedAt IS NULL');

        // Filter inactives out.
        if ('report' == $entityType) {
            $query->andWhere('e.sysStatus = \'Aktiv\'');
        } elseif ('system' == $entityType) {
            $query->andWhere('e.sysStatus <> \'Systemet bruges ikke længere\'');
        }

        // Get the groups the user can search in.
        if (!empty($formParameters['groups'])) {
            $groups = $this->entityManager
                ->getRepository(UserGroup::class)
                ->findBy([
                    'id' => $formParameters['groups'],
                ]);

            foreach ($groups as $group) {
                $query->andWhere(
                    $query
                        ->expr()
                        ->isMemberOf(':group'.$group->getId(), 'e.groups')
                );
                $query->setParameter(':group'.$group->getId(), $group);
            }
        }

        if (
            isset($formParameters['self_service'])
            && '' != $formParameters['self_service']
        ) {
            $item = $this->entityManager
                ->getRepository(SelfServiceAvailableFromItem::class)
                ->findOneBy([
                    'id' => $formParameters['self_service'],
                ]);
            if (null != $item) {
                $query->andWhere(
                    ':self_service MEMBER OF e.selfServiceAvailableFromItems'
                );
                $query->setParameter('self_service', $item);
            }
        }

        if (
            isset($formParameters['search'])
            && '' != $formParameters['search']
        ) {
            $query->andWhere('e.name LIKE :name');
            $query->setParameter('name', '%'.$formParameters['search'].'%');
        }

        if (
            isset($formParameters['subowner'])
            && '' != $formParameters['subowner']
        ) {
            $query->andWhere('e.sysOwnerSub = :subowner');
            $query->setParameter('subowner', $formParameters['subowner']);
        }

        return $query;
    }

    /**
     * Get sub-owners for selected group.
     */
    private function getSubOwnerOptions(
        mixed $repository,
        mixed $selectedGroups,
    ): mixed {
        $groups = $this->entityManager
            ->getRepository(UserGroup::class)
            ->findBy([
                'id' => $selectedGroups,
            ]);

        $subOwnersQueryBuilder = $repository->createQueryBuilder('e');
        $subOwnersQueryBuilder->select('DISTINCT e.sysOwnerSub');
        $subOwnersQueryBuilder->andWhere('e.sysOwnerSub IS NOT NULL');

        // Filter inactives out.
        $subOwnersQueryBuilder->andWhere('e.archivedAt IS NULL');

        $class = $repository->getClassName();
        if (Report::class === $class) {
            $subOwnersQueryBuilder->andWhere('e.sysStatus = \'Aktiv\'');
        } elseif (System::class === $class) {
            $subOwnersQueryBuilder->andWhere(
                'e.sysStatus <> \'Systemet bruges ikke længere\''
            );
        }

        foreach ($groups as $group) {
            $subOwnersQueryBuilder->andWhere(
                $subOwnersQueryBuilder
                    ->expr()
                    ->isMemberOf(':group'.$group->getId(), 'e.groups')
            );
            $subOwnersQueryBuilder->setParameter(
                ':group'.$group->getId(),
                $group
            );
        }

        $subOwners = $subOwnersQueryBuilder->getQuery()->getResult();

        return array_reduce(
            $subOwners,
            function ($carry, $item) {
                $carry[$item['sysOwnerSub']] = $item['sysOwnerSub'];

                return $carry;
            },
            []
        );
    }

    /**
     * Get options for self-service filter.
     *
     * @param string $entityType
     *
     * @return array<string, int>
     */
    private function getSelfServiceOptions(string $entityType): array
    {
        $selfServiceOptions = [];
        if ('system' == $entityType) {
            /* @var Collection $selfServiceAvailableFromItems */
            $selfServiceAvailableFromItems = $this->entityManager
                ->getRepository(SelfServiceAvailableFromItem::class)
                ->findAll();
            /* @var SelfServiceAvailableFromItem $item */
            foreach ($selfServiceAvailableFromItems as $item) {
                $selfServiceOptions[$item->getName()] = $item->getId();
            }
        }

        return $selfServiceOptions;
    }

    /**
     * Get filter form builder.
     *
     * @param mixed $userGroupsThemesAndCategories
     * @param array<string, mixed> $formParameters
     * @param mixed $subownerOptions
     * @param bool $filterThemes
     * @param bool $filterCategories
     * @param array<int> $filterSelfServiceOptions
     *
     * @return FormBuilderInterface
     */
    private function getFilterFormBuilder(
        mixed $userGroupsThemesAndCategories,
        array $formParameters,
        mixed $subownerOptions,
        bool $filterThemes = false,
        bool $filterCategories = false,
        array $filterSelfServiceOptions = [],
    ): FormBuilderInterface {
        $filterFormBuilder = $this->createFormBuilder();
        $filterFormBuilder->add('groups', ChoiceType::class, [
            'label' => 'filter.groups',
            'placeholder' => 'filter.placeholder.groups',
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'multiple' => true,
            'attr' => [
                'class' => 'form-control',
                'data-placeholder' => $this->translator->trans(
                    'filter.placeholder.groups'
                ),
            ],
            'required' => false,
            'data' => $formParameters['groups'] ?? null,
        ]);
        $filterFormBuilder->add('subowner', ChoiceType::class, [
            'label' => 'filter.subowner',
            'placeholder' => 'filter.placeholder.subowner',
            'choices' => $subownerOptions,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'disabled' => 0 == count($subownerOptions),
            'data' => $formParameters['subowner'] ?? null,
        ]);
        if ($filterThemes) {
            $filterFormBuilder->add('theme', ChoiceType::class, [
                'label' => 'filter.theme',
                'placeholder' => 'filter.placeholder.theme',
                'choices' => array_flip(
                    $userGroupsThemesAndCategories['themes']
                ),
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'data' => $formParameters['theme'] ?? null,
            ]);
        }
        if ($filterCategories) {
            $filterFormBuilder->add('category', ChoiceType::class, [
                'label' => 'filter.category',
                'placeholder' => 'filter.placeholder.category',
                'choices' => array_flip(
                    $userGroupsThemesAndCategories['categories']
                ),
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'data' => $formParameters['category'] ?? null,
            ]);
        }
        if (count($filterSelfServiceOptions) > 0) {
            $filterFormBuilder->add('self_service', ChoiceType::class, [
                'label' => 'filter.self_service',
                'placeholder' => 'filter.placeholder.self_service',
                'choices' => $filterSelfServiceOptions,
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'data' => $formParameters['self_service'] ?? null,
            ]);
        }
        $filterFormBuilder->add('search', TextType::class, [
            'label' => 'filter.search',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'filter.placeholder.search',
            ],
            'required' => false,
            'data' => $formParameters['search'] ?? null,
        ]);
        $filterFormBuilder->add('save', SubmitType::class, [
            'label' => 'filter.submit',
        ]);

        return $filterFormBuilder;
    }

    /**
     * Overrides EasyAdmin show action.
     *
     * @return RedirectResponse|Response
     */
    public function showAction(): RedirectResponse|Response
    {
        $entityArray = $this->entity;
        if (
            Report::class == $entityArray['class']
            || System::class == $entityArray['class']
        ) {
            $entity = $this->getEntity($entityArray['class'], $_GET['id']);
            $accessGranted = $this->isGranted('show', $entity);

            if (!$accessGranted) {
                $this->addFlash(
                    'error',
                    $this->translator->trans('flash.access_denied')
                );

                return $this->redirectToRoute('list', [
                    'entityType' => strtolower($entityArray['name']),
                ]);
            }
        }

        return parent::showAction();
    }

    /**
     * @param string $entityName
     * @param mixed $id
     *
     * @return object|null
     */
    private function getEntity(string $entityName, mixed $id): ?object
    {
        return $this->entityManager->getRepository($entityName)->find($id);
    }
}
