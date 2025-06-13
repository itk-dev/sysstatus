<?php

namespace App\Controller\Admin;

use App\Entity\UserGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class GroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserGroup::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(new TranslatableMessage('UserGroup'))
            ->setEntityLabelInPlural(new TranslatableMessage('UserGroups'))
        ;
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield ChoiceField::new('roles')->setChoices([
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
        ])->allowMultipleChoices()->renderExpanded()->setEmptyData(false);
        yield CollectionField::new('reports')
            ->setTemplatePath('admin/collection.html.twig');
        yield AssociationField::new('systems')
            ->setTemplatePath('admin/collection.html.twig');
        yield ArrayField::new('systemThemes')
            ->setTemplatePath('admin/collection.html.twig');
        yield ArrayField::new('reportThemes')
            ->setTemplatePath('admin/collection.html.twig');
        yield ArrayField::new('users')
            ->setTemplatePath('admin/collection.html.twig');
    }
}
