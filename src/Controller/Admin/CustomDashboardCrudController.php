<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use App\Entity\System;
use App\Entity\UserGroup;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class CustomDashboardCrudController extends AbstractSystatusDashboardController
{
    use EntityFilterTrait;

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
    #[Route(path: '/dashboard')]
    #[\Override]
    public function index(): Response
    {
        return $this->redirectToRoute('dashboard', ['entityType' => 'report']);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/dashboard/{entityType}', name: 'dashboard')]
    public function dashboard(Request $request, string $entityType): Response
    {
        $queryParameters = $request->query;

        $filterParameters = $this->getFilterParameters($request);
        $userGroups = $this->entityManager
            ->getRepository(UserGroup::class)
            ->findAll();

        // Get a query for the entity type.
        $repository = $this->getRepository($entityType);
        $query = $repository->createQueryBuilder('e');
        $query = $this->applyFilters($query, $filterParameters, $this->getEntityClassName($entityType));

        $paginator = $this->paginator->paginate(
            $query,
            $queryParameters->get('page', 1),
            10
        );

        $availableCategories = [];
        $themes = [];

        if (!empty($filterParameters['groups'])) {
            $groups = $this->entityManager
                ->getRepository(UserGroup::class)
                ->findBy([
                    'id' => $filterParameters['groups'],
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
                if ('' != $filterParameters['theme']) {
                    if ($theme->getId() != $filterParameters['theme']) {
                        continue;
                    }
                }

                if (!isset($themes[$theme->getId()])) {
                    $themes[$theme->getId()] = $theme;

                    foreach ($theme->getOrderedCategories() as $category) {
                        if ('' != $filterParameters['category']) {
                            if (
                                $category->getId() !=
                                $filterParameters['category']
                            ) {
                                continue;
                            }
                        }

                        $availableCategories[$category->getId()] = $category;
                    }
                }
            }
        }

        $filterFormBuilder = $this->createFilterFormBuilder()
            ->setAction(
                $this->generateUrl('dashboard', ['entityType' => $entityType])
            );

        $this->buildCustomFilters(
            $request,
            $this->getEntityClassName($entityType),
            $filterFormBuilder,
            filterThemes: true,
            filterCategories: true
        );

        return $this->render('dashboard.html.twig', [
            'paginator' => $paginator,
            'categories' => $availableCategories,
            'custom_filters' => $filterFormBuilder->getForm()->createView(),
            'entityType' => $entityType,
        ]);
    }

    /**
     * Get repository for entity type.
     */
    private function getRepository(
        string $entityType,
    ): EntityRepository {
        $className = $this->getEntityClassName($entityType);

        return $this->entityManager->getRepository($className);
    }

    private function getEntityClassName(string $entityType): ?string
    {
        return match ($entityType) {
            'system' => System::class,
            'report' => Report::class,
            default => null,
        };
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
     * Overrides EasyAdmin show action.
     *
     * @return RedirectResponse|Response
     */
    public function show(): RedirectResponse|Response
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
                    'entityType' => strtolower((string) $entityArray['name']),
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
