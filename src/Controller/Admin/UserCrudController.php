<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $username = Field::new('username');
        $email = Field::new('email');
        $enabled = Field::new('enabled');
        $lastLogin = Field::new('lastLogin');
        $roles = Field::new('roles');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $email, $enabled, $lastLogin, $roles ];
        }
//        elseif(Crud::PAGE_DETAIL === $pageName) {
//            return [$title, $coll_answers_detail];
//        }
//        elseif(Crud::PAGE_NEW === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
//        elseif(Crud::PAGE_EDIT === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
    }

}
