<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class ReportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Report::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE, Action::NEW)
        ;

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $title = TextField::new('sys_title')->setLabel('entity.report.sys_title');
        $internalId = TextField::new('sys_internal_id')->setLabel('entity.report.sys_internal_id');
        $edocUrl = UrlField::new('edoc_url')->setLabel('entity.report.edoc_url');
//        $sys_link = UrlField::new('sys_link')->setFilename('Link til anmeldelse')->setLabel('entity.report.sys_link');
        $name = TextField::new('name')->setLabel('entity.report.name');
        $text = TextField::new('text')->setLabel('entity.report.text');
        $systemOwner = TextField::new('sys_system_owner')->setLabel('entity.report.sys_system_owner');
        $groups = AssociationField::new('groups')->setLabel('entity.report.groups');
        $answerarea = AssociationField::new('answerarea')->setLabel('entity.report.answers')->setTemplatePath('easy_admin_overrides/answers_show.html.twig');
        $alternativeTitle = TextField::new('sys_alternative_title')->setLabel('entity.report.sys_alternative_title');
        $sys_updated = DateTimeField::new('sys_updated')->setLabel('entity.report.sys_updated');
        $owner = TextField::new('sys_owner')->setLabel('entity.report.sys_owner');
        $confidentialInformation = BooleanField::new('sys_confidential_information')->setLabel('entity.report.sys_confidential_information');
        $purpose = TextField::new('sys_purpose')->setLabel('entity.report.sys_purpose');
        $classification = TextField::new('sys_classification')->setLabel('entity.report.sys_classification');
        $dateForRevision = DateTimeField::new('sys_date_for_revision')->setLabel('entity.report.sys_date_for_revision');
        $persons = TextField::new('sys_persons')->setLabel('entity.report.sys_persons');
        $informationTypes = TextField::new('sys_information_types')->setLabel('entity.report.sys_information_types');
        $dataSentTo = TextField::new('sys_data_sent_to')->setLabel('entity.report.sys_data_sent_to');
        $dataComeFrom = TextField::new('sys_data_come_from')->setLabel('entity.report.sys_data_come_from');
        $dataLocation = TextField::new('sys_data_location')->setLabel('entity.report.sys_data_location');
        $deletionDate = TextField::new('sys_latest_deletion_date')->setLabel('entity.report.sys_latest_deletion_date');
        $dataWorthSaving = TextField::new('sys_data_worth_saving')->setLabel('entity.report.sys_data_worth_saving');
        $dataProcessors = TextField::new('sys_data_processors')->setLabel('entity.report.sys_data_processors');
        $dataProcessingAgreement = TextField::new('sys_data_processing_agreement')->setLabel('entity.report.sys_data_processing_agreement');
        $dataProcessingAgreementLink = TextField::new('sys_data_processing_agreement_link')->setLabel('entity.report.sys_data_processing_agreement_link');
        $auditorStatement = TextField::new('sys_auditor_statement')->setLabel('entity.report.sys_auditor_statement');
        $auditorStatementLink = TextField::new('sys_auditor_statement_link')->setLabel('entity.report.sys_auditor_statement_link');
        $dataToScience = TextField::new('sys_data_to_science')->setLabel('entity.report.sys_data_to_science');
        $usage = TextField::new('sys_usage')->setLabel('entity.report.sys_usage');
        $requestForInsight = TextField::new('sys_request_for_insight')->setLabel('entity.report.sys_request_for_insight');
        $dateUse = DateTimeField::new('sys_date_use')->setLabel('entity.report.sys_date_use');
        $status = TextField::new('sys_status')->setLabel('entity.report.sys_status');
        $remarks = TextField::new('sys_remarks')->setLabel('entity.report.sys_remarks');
        $internalInformation = TextField::new('sys_internal_information')->setLabel('entity.report.sys_internal_information');
        $obligationToInform = TextField::new('sys_obligation_to_inform')->setLabel('entity.report.sys_obligation_to_inform');
        $legalBasis = TextField::new('sys_legal_basis')->setLabel('entity.report.sys_legal_basis');
        $consent = TextField::new('sys_consent')->setLabel('entity.report.sys_consent');
        $impactAnalysis = TextField::new('sys_impact_analysis')->setLabel('entity.report.sys_impact_analysis');
        $impactAnalysisLink = TextField::new('sys_impact_analysis_link')->setLabel('entity.report.sys_impact_analysis_link');
        $authorizationProcedure = TextField::new('sys_authorization_procedure')->setLabel('entity.report.sys_authorization_procedure');
        $version = TextField::new('sys_version')->setLabel('entity.report.sys_version');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$title];
        }
        elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$title, $internalId, $edocUrl,
                $name, $text, $systemOwner, $groups, $answerarea, $alternativeTitle, $sys_updated, $owner,
                $confidentialInformation, $purpose, $classification, $dateForRevision, $persons, $informationTypes,
                $dataSentTo, $dataComeFrom, $dataLocation, $deletionDate, $dataWorthSaving, $dataProcessors,
                $dataProcessingAgreement, $dataProcessingAgreementLink, $auditorStatement, $auditorStatementLink,
                $dataToScience, $usage, $requestForInsight, $dateUse, $status, $remarks, $internalInformation, $obligationToInform,
                $legalBasis, $consent, $impactAnalysis, $impactAnalysisLink, $authorizationProcedure, $version];
        }
         elseif (Crud::PAGE_EDIT === $pageName) {
            return [];
        } else {
            throw new \Exception('Invalid page: '.$pageName);
        }
    }
}
