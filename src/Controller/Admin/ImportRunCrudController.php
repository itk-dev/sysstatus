<?php

namespace App\Controller\Admin;

use App\Entity\ImportRun;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImportRunCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImportRun::class;
    }

    public function configureActions(Actions $actions): Actions
    {


        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::EDIT, Action::DELETE)



        ;

        return $actions;
    }


    public function configureFields(string $pageName): iterable
    {

        $id = IdField::new('id');
        $type = TextField::new('type');
        $output = TextField::new('output');
        $datetime = DateTimeField::new('datetime');
        $result = BooleanField::new('result')->renderAsSwitch(false);

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $type, $datetime, $result];
        }
        elseif(Crud::PAGE_DETAIL === $pageName) {
            return [$id, $type, $datetime, $result, $output ];
        }
        elseif(Crud::PAGE_NEW === $pageName) {
            return [$type, $datetime, $result, $output ];
        }
//        elseif(Crud::PAGE_EDIT === $pageName) {
//            return [$title, $editor_description, $editor_instructions, $editor_preparations, $coll_questions, $coll_configuration ];
//        }
    }

}