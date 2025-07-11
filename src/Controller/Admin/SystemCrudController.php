<?php

namespace App\Controller\Admin;

use App\Entity\System;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class SystemCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return System::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::NEW)
        ;

        return $actions;
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->setLabel('entity.system.id');
        $text = TextField::new('text')->setLabel('entity.system.text');
        $text_editor = TextareaField::new('text')->setLabel('entity.system.text');
        $name = TextField::new('name')->setLabel('entity.system.name');
        $sys_title = TextField::new('sys_title')->setDisabled(true)->setLabel('entity.system.sys_title');
        $sys_owner = TextField::new('sys_owner_sub')->setLabel('entity.system.sys_owner_sub');
        $sys_system_owner = TextField::new('sys_system_owner')->setLabel('entity.system.sys_system_owner');
        $sys_link = UrlField::new('sys_link')->setLabel('entity.system.sys_link');
        $sys_self_service_url = AssociationField::new('selfServiceAvailableFromItems')->setLabel('entity.system.selfServiceAvailableFromItems');
        $edoc_url = TextField::new('edoc_url')->setLabel('entity.system.edoc_url');
        $groups = AssociationField::new('groups')->setLabel('entity.system.groups');
        $answerarea = CollectionField::new('answerarea')->setTemplatePath('easy_admin_overrides/answers_show.html.twig')->setLabel('Smileys');
        $sys_updated = DateField::new('sys_updated')->setLabel('entity.system.sys_updated');
        $sys_description = TextField::new('sys_description')->setLabel('entity.system.sys_description');
        $sys_owner_subdepartment = TextField::new('sys_owner_subdepartment')->setLabel('entity.system.sys_owner_subdepartment');
        $sys_emergency_setup = TextField::new('sys_emergency_setup')->setLabel('entity.system.sys_emergency_setup');
        $sys_contractor = TextField::new('sys_contractor')->setLabel('entity.system.sys_contractor');
        $sys_urgency_rating = TextField::new('sys_urgency_rating')->setLabel('entity.system.sys_urgency_rating');
        $sys_number_of_users = TextField::new('sys_number_of_users')->setLabel('entity.system.sys_number_of_users');
        $sys_technical_documentation = TextField::new('sys_technical_documentation')->setLabel('entity.system.sys_technical_documentation');
        $sys_external_dependencies = TextField::new('sys_external_dependencies')->setLabel('entity.system.sys_external_dependencies');
        $sys_important_information = TextField::new('sys_important_information')->setLabel('entity.system.sys_important_information');
        $sys_superuser_organization = TextField::new('sys_superuser_organization')->setLabel('entity.system.sys_superuser_organization');
        $sys_itsecurity_category = TextField::new('sys_itsecurity_category')->setLabel('entity.system.sys_itsecurity_category');
        $sys_link_to_security_review = TextField::new('sys_link_to_security_review')->setLabel('entity.system.sys_link_to_security_review');
        $sys_link_to_contract = TextField::new('sys_link_to_contract')->setLabel('entity.system.sys_link_to_contract');
        $sys_end_of_contract = DateField::new('sys_end_of_contract')->setLabel('entity.system.sys_end_of_contract');
        $sys_status = TextField::new('sys_status')->setLabel('entity.system.sys_status');
        $sys_open_data = TextField::new('sys_open_data')->setLabel('entity.system.sys_open_data');
        $sys_open_source = TextField::new('sys_open_source')->setLabel('entity.system.sys_open_source');
        $sys_digital_post = TextField::new('sys_digital_post')->setLabel('entity.system.sys_system_category');
        $sys_digital_transactions_pr_year = TextField::new('sys_digital_transactions_pr_year')->setLabel('entity.system.sys_digital_transactions_pr_year');
        $sys_total_transactions_pr_year = TextField::new('sys_total_transactions_pr_year')->setLabel('entity.system.sys_total_transactions_pr_year');
        //        $selfServiceAvailableFromItems = TextField::new('selfServiceAvailableFromItems')->setLabel('entity.system.selfServiceAvailableFromItems');
        $sys_alternative_title = TextField::new('sys_alternative_title')->setLabel('sys_alternative_title');
        $sys_version = TextField::new('sys_version')->setLabel('sys_version');

        //      $coll_question = CollectionField::new('questions')->setEntryType('App\Form\CategoryType');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$name, $sys_owner, $sys_system_owner, $sys_link,  $text, $sys_self_service_url, $groups];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $text, $sys_system_owner, $answerarea, $groups, $sys_title, $edoc_url, $sys_link, $sys_updated, $sys_description, $sys_owner, $sys_owner_subdepartment,
                $sys_emergency_setup, $sys_contractor, $sys_urgency_rating, $sys_number_of_users, $sys_technical_documentation, $sys_external_dependencies, $sys_important_information,
                $sys_superuser_organization, $sys_itsecurity_category, $sys_link_to_security_review, $sys_link_to_contract, $sys_end_of_contract, $sys_status, $sys_open_data,
                $sys_open_source, $sys_digital_post, $sys_digital_transactions_pr_year, $sys_total_transactions_pr_year, /* $selfServiceAvailableFromItems, */ $sys_alternative_title,
                $sys_version,
            ];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$sys_title, $text_editor, $groups, $edoc_url];
        } else {
            throw new \Exception('Invalid page: '.$pageName);
        }
    }
}
