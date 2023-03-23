<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
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
        $username = TextField::new('username');
        $email = TextField::new('email');
        $groups = CollectionField::new('groups');
        $enabled = TextField::new('enabled');
        $lastLogin = DateTimeField::new('lastLogin');
        $roles = ArrayField::new('roles');



        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $email, $enabled, $lastLogin, $roles ];
        }
        elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$username, $email, $groups, $enabled, $lastLogin, $roles ];
        }
        elseif(Crud::PAGE_NEW === $pageName) {
            return [$username, $email, $groups, $enabled, $lastLogin, $roles ];
        }
//        elseif(Crud::PAGE_EDIT === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
    }

}
