<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\QuestionType;
use App\Form\ThemeCategoryType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
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
        yield TextField::new('name')->setLabel('entity.category.name');
        yield TextField::new('createdBy')
            ->onlyOnDetail();
        yield TextField::new('updatedBy')
            ->onlyOnDetail();
        yield TimeField::new('createdAt')
            ->onlyOnDetail();
        yield TimeField::new('updatedAt')
            ->onlyOnDetail();
        // See templates/admin/form.html.twig for details on how we show this as a table.
        yield CollectionField::new('questions')->setLabel('entity.category.questions')
            ->setTemplatePath(Crud::PAGE_DETAIL === $pageName ? 'admin/collection_plain.html.twig' : 'admin/collection.html.twig')
            ->setEntryType(QuestionType::class)
            ->renderExpanded();
        yield CollectionField::new('themeCategories')->setEntryType(ThemeCategoryType::class)
            ->onlyOnDetail();
    }
}
