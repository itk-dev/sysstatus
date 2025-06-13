<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeCategoryType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ThemeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Theme::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            // https://symfony.com/bundles/EasyAdminBundle/current/design.html#form-field-templates
            ->addFormTheme('admin/form.html.twig');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name')->setLabel('entity.theme.name');
        // See templates/admin/form.html.twig for details on how we show this as a table.
        yield CollectionField::new('themeCategories')->setLabel('entity.theme.categories')
            ->setTemplatePath('admin/collection.html.twig')
            ->setEntryType(ThemeCategoryType::class)
            ->renderExpanded()
            ->setFormTypeOptions([
                'by_reference' => false, // important for OneToMany associations
            ])
        ;
        yield AssociationField::new('systemGroups')->setLabel('entity.theme.system_groups')
            ->setTemplatePath('admin/collection.html.twig')
            ->setFormTypeOption('by_reference', false);
        yield AssociationField::new('reportGroups')->setLabel('entity.theme.report_groups')
            ->setTemplatePath('admin/collection.html.twig')
            ->setFormTypeOption('by_reference', false);
    }
}
