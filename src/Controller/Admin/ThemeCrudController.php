<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeCategoryType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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
        return $crud
            // https://symfony.com/bundles/EasyAdminBundle/current/design.html#form-field-templates
            ->addFormTheme('admin/form.html.twig');
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');

        $sysgroups = AssociationField::new('systemGroups')
            ->setFormTypeOption('by_reference', false)->setLabel('Systemer')
        ;

        $repgroups = AssociationField::new('reportGroups')
            ->setFormTypeOption('by_reference', false)->setLabel('Anmeldelser')
        ;

        // See templates/admin/form.html.twig for details on how we show this as a table.
        $categoriesField = CollectionField::new('themeCategories')->setLabel('Kategorier')
            ->setEntryType(ThemeCategoryType::class)
            ->renderExpanded()
            ->setFormTypeOptions([
                'by_reference' => false, // important for OneToMany associations
            ])
        ;

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $sysgroups, $repgroups, $categoriesField];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $sysgroups, $repgroups, $categoriesField];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $sysgroups, $repgroups, $categoriesField];
        } else {
            throw new \Exception('Invalid page: '.$pageName);
        }
    }
}
