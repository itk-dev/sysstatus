<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use App\Entity\SelfServiceAvailableFromItem;
use App\Entity\System;
use App\Entity\UserGroup;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

trait EntityFilterTrait
{
    protected function createFilterFormBuilder(): FormBuilderInterface
    {
        // We don't want a CSRF token on our filter form.
        return $this->createFormBuilder(options: ['csrf_protection' => false])
            ->setMethod(Request::METHOD_GET);
    }

    protected function buildCustomFilters(
        Request $request,
        string $entityType,
        FormBuilderInterface $builder,
        bool $filterThemes = false,
        bool $filterCategories = false,
    ): void {
        $userGroups = $this->entityManager->getRepository(UserGroup::class)->findAll();
        $userGroupsThemesAndCategories = $this->getUserGroupsThemesAndCategories($userGroups, $entityType);

        $formParameters = array_merge([
            'groups' => [],
            'subowner' => '',
            'search' => '',
        ], $request->get('form') ?: []);

        $repository = $this->entityManager->getRepository($entityType);
        // Get sub owners if a group has been selected.
        $subOwnerOptions = $this->getSubOwnerOptions($repository, $formParameters['groups']);

        $filterSelfServiceOptions = $this->getSelfServiceOptions($entityType);

        $placeholder = $this->translator->trans('filter.placeholder.groups');
        $builder->add('groups', ChoiceType::class, [
            'label' => 'filter.groups',
            'placeholder' => $placeholder,
            'choices' => array_flip($userGroupsThemesAndCategories['groups']),
            'multiple' => true,
            'attr' => [
                // @todo Find documentation reference for why setting data-ea-widget actually works
                // (https://github.com/search?q=repo%3AEasyCorp%2FEasyAdminBundle%20data-ea-widget&type=code)
                'data-ea-widget' => 'ea-autocomplete',
                'data-placeholder' => $placeholder,
            ],
            'required' => false,
            'data' => $formParameters['groups'] ?? null,
        ]);

        $placeholder = $this->translator->trans('filter.placeholder.subowner');
        $builder->add('subowner', ChoiceType::class, [
            'label' => 'filter.subowner',
            'placeholder' => 'filter.placeholder.subowner',
            'choices' => $subOwnerOptions,
            'attr' => [
                'data-ea-widget' => 'ea-autocomplete',
                'data-placeholder' => $placeholder,
            ],
            'required' => false,
            'disabled' => empty($subOwnerOptions),
            'data' => $formParameters['subowner'] ?? null,
        ]);

        if ($filterThemes) {
            $placeholder = $this->translator->trans('filter.placeholder.theme');
            $builder->add('theme', ChoiceType::class, [
                'label' => 'filter.theme',
                'placeholder' => $placeholder,
                'choices' => array_flip($userGroupsThemesAndCategories['themes']),
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                    'data-placeholder' => $placeholder,
                ],
                'required' => false,
                'data' => $formParameters['theme'] ?? null,
            ]);
        }
        if ($filterCategories) {
            $placeholder = $this->translator->trans('filter.placeholder.category');
            $builder->add('category', ChoiceType::class, [
                'label' => 'filter.category',
                'placeholder' => $placeholder,
                'choices' => array_flip($userGroupsThemesAndCategories['categories']),
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                    'data-placeholder' => $placeholder,
                ],
                'required' => false,
                'data' => $formParameters['category'] ?? null,
            ]);
        }
        if (count($filterSelfServiceOptions) > 0) {
            $placeholder = $this->translator->trans('filter.placeholder.self_service');
            $builder->add('self_service', ChoiceType::class, [
                'label' => 'filter.self_service',
                'placeholder' => $placeholder,
                'choices' => $filterSelfServiceOptions,
                'attr' => [
                    'data-ea-widget' => 'ea-autocomplete',
                    'data-placeholder' => $placeholder,
                ],
                'required' => false,
                'data' => $formParameters['self_service'] ?? null,
            ]);
        }

        $placeholder = $this->translator->trans('filter.placeholder.search');
        $builder->add('search', TextType::class, [
            'label' => 'filter.search',
            'attr' => [
                'placeholder' => $placeholder,
            ],
            'required' => false,
            'data' => $formParameters['search'] ?? null,
        ]);
        $builder->add('save', SubmitType::class, [
            'label' => 'filter.submit',
        ]);
    }

    /**
     * Get array of a user's groups, themes and categories.
     *
     * @param UserGroup[] $userGroups
     *
     * @return mixed
     */
    private function getUserGroupsThemesAndCategories(array $userGroups, string $entityType)
    {
        return array_reduce($userGroups,
            function ($carry, UserGroup $group) use ($entityType) {
                $carry['groups'][$group->getId()] = $group->getName();

                $groupThemes = 'report' == $entityType ? $group->getReportThemes() : $group->getSystemThemes();

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

    /**
     * Get subowners for selected group.
     *
     * @param EntityRepository<object> $repository
     * @param UserGroup[] $selectedGroups
     *
     * @return mixed
     */
    private function getSubOwnerOptions(EntityRepository $repository, array $selectedGroups)
    {
        $groups = $this->entityManager->getRepository(UserGroup::class)->findBy([
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
     * Get options for self service filter.
     *
     * @return array<string, mixed>
     */
    private function getSelfServiceOptions(string $entityType): array
    {
        $selfServiceOptions = [];
        if (System::class === $entityType) {
            /* @var \Doctrine\Common\Collections\Collection $selfServiceAvailableFromItems */
            $selfServiceAvailableFromItems = $this->entityManager->getRepository(SelfServiceAvailableFromItem::class)->findAll();
            /* @var SelfServiceAvailableFromItem $item */
            foreach ($selfServiceAvailableFromItems as $item) {
                $selfServiceOptions[$item->getName()] = $item->getId();
            }
        }

        return $selfServiceOptions;
    }

    /**
     * @return array<string, mixed>
     */
    private function getFilterParameters(Request $request): array
    {
        $formData = (array) ($request->query->all()['form'] ?? null);

        return array_merge(
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
    }

    /**
     * Apply common filters for report and system query.
     *
     * @param QueryBuilder $query
     * @param array<string, mixed> $filterParameters
     * @param string|null $entityType
     *
     * @return QueryBuilder
     */
    private function applyFilters(
        QueryBuilder $query,
        array $filterParameters,
        ?string $entityType = null,
        ?string $alias = null,
    ): QueryBuilder {
        $alias ??= $query->getRootAliases()[0];
        if (empty($alias)) {
            throw new \RuntimeException('Cannot get root alias for query');
        }

        // Get the groups the user can search in.
        if (!empty($filterParameters['groups'])) {
            $groups = $this->entityManager
                ->getRepository(UserGroup::class)
                ->findBy([
                    'id' => $filterParameters['groups'],
                ]);

            foreach ($groups as $group) {
                $query->andWhere(
                    $query
                        ->expr()
                        ->isMemberOf(':group'.$group->getId(), $alias.'.groups')
                );
                $query->setParameter(':group'.$group->getId(), $group);
            }
        }

        if (
            isset($filterParameters['self_service'])
            && '' != $filterParameters['self_service']
        ) {
            $item = $this->entityManager
                ->getRepository(SelfServiceAvailableFromItem::class)
                ->findOneBy([
                    'id' => $filterParameters['self_service'],
                ]);
            if (null != $item) {
                $query->andWhere(
                    ':self_service MEMBER OF '.$alias.'.selfServiceAvailableFromItems'
                );
                $query->setParameter('self_service', $item);
            }
        }

        if (
            isset($filterParameters['search'])
            && '' != $filterParameters['search']
        ) {
            $query->andWhere($alias.'.name LIKE :name');
            $query->setParameter('name', '%'.$filterParameters['search'].'%');
        }

        if (
            isset($filterParameters['subowner'])
            && '' != $filterParameters['subowner']
        ) {
            $query->andWhere($alias.'.sysOwnerSub = :subowner');
            $query->setParameter('subowner', $filterParameters['subowner']);
        }

        return $query;
    }
}
