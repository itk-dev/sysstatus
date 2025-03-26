<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ;

        return $actions;
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $username = TextField::new('username');
        $password = TextField::new('password');
        $email = EmailField::new('email');
        $groups = AssociationField::new('groups');
        $enabled = BooleanField::new('enabled');
        $lastLogin = DateTimeField::new('lastLogin');

        $roles = ArrayField::new('roles');

        $choice_roles = ChoiceField::new('roles')->setChoices([
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
        ])->allowMultipleChoices(true)->renderExpanded()->setEmptyData(false);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $email, $enabled, $lastLogin, $roles];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$username, $email, $groups, $enabled, $lastLogin, $roles];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$username, $email, $groups, $enabled, $password, $choice_roles];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$username, $email, $groups, $enabled, $password, $choice_roles];
        } else {
            throw new \Exception('Invalid page: '.$pageName);
        }
    }
}
