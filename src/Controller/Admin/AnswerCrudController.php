<?php

namespace App\Controller\Admin;

use App\DBAL\Types\SmileyType;
use App\Entity\Answer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AnswerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::EDIT)
            ->add(Crud::PAGE_NEW, Action::NEW)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
//        $question = TextField::new('question');
        $question = AssociationField::new('question')->setFormTypeOption('disabled', true);
        $choice = ChoiceField::new('smiley')->setChoices([
            'Green' => SmileyType::GREEN,
            'Red' => SmileyType::RED,
            'Blue' => SmileyType::BLUE,
            'Yellow' => SmileyType::YELLOW,
        ]);
        $text = TextField::new('note');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$question];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$question];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$question];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$question, $choice, $text];
        }
    }
}
