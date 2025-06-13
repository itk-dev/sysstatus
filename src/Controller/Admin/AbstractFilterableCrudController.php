<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractFilterableCrudController extends AbstractCrudController
{
    use EntityFilterTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            // Disable the search box (https://symfony.com/bundles/EasyAdminBundle/current/crud.html#search-order-and-pagination-options)
            ->setSearchFields(null)
            ->overrideTemplate('crud/index', 'admin/crud/index.html.twig');
    }

    #[\Override]
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $responseParameters = parent::configureResponseParameters($responseParameters);
        if ($pageName = $responseParameters->get('pageName')) {
            if (Crud::PAGE_INDEX === $pageName) {
                // Add our custom filters on the index page.
                $filterFormBuilder = $this->createFilterFormBuilder();
                $this->buildCustomFilters(
                    $this->getContext()->getRequest(),
                    $this->getContext()->getEntity()->getFqcn(),
                    $filterFormBuilder
                );
                $responseParameters->set('custom_filters', $filterFormBuilder->getForm()->createView());
            }
        }

        return $responseParameters;
    }

    #[\Override]
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        $builder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $filterParameters = $this->getFilterParameters($searchDto->getRequest());

        $this->applyFilters($builder, $filterParameters, $entityDto->getFqcn());

        return $builder;
    }
}
