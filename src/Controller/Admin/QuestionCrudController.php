<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions

            ->add(Crud::PAGE_EDIT, Action::EDIT)
            ->disable(Action::NEW, Action::DELETE, Action::DETAIL, Action::INDEX)
        ;
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->setLabel('entity.system.id');

        if (Crud::PAGE_EDIT === $pageName) {
            return [$id];
        } else {
            throw new \Exception('Invalid page: '.$pageName);
        }
    }
}
