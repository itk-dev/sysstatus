<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Group::class;
    }

    public function configureActions(Actions $actions): Actions
    {


        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)

        ;

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $roles = ArrayField::new('roles');
        $system = ArrayField::new('systems');


        $asoc_report = AssociationField::new('reports');
        $asoc_systems = AssociationField::new('systems');

        $choice_roles = ChoiceField::new('roles')->setChoices([
            'User' => "ROLE_USER",
            'Admin' => "ROLE_ADMIN"
        ])->allowMultipleChoices(true)->renderExpanded()->setEmptyData(false);


        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $asoc_report,$asoc_systems];
        }
        elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $roles, $system, ];
        }
        elseif(Crud::PAGE_NEW === $pageName) {
            return [$name, $choice_roles];
        }
//        elseif(Crud::PAGE_EDIT === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
    }
}
