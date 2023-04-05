<?php

namespace App\Controller\Admin;

use App\Entity\System;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class CustomSystemDashboard extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return System::class;
    }

    public function configureFilters(Filters $filters): Filters
    {

        $filters->add('name');
        return parent::configureFilters($filters); // TODO: Change the autogenerated stub
    }

    public function configureActions(Actions $actions): Actions
    {

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::EDIT, Action::DELETE, Action::NEW)
        ;

        return $actions;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');



        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name];
        }
//        elseif(Crud::PAGE_DETAIL === $pageName) {
//            return [$username, $email, $groups, $enabled, $lastLogin, $roles ];
//        }
//        elseif(Crud::PAGE_NEW === $pageName) {
//            return [$name, $coll_question];
//        }
//        elseif(Crud::PAGE_EDIT === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
    }

}