<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Translation\TranslatableMessage;

class ReportCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return Report::class;
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

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        switch ($pageName) {
            case Crud::PAGE_INDEX:
                // Cf. https://github.com/itk-dev/sysstatus/blob/5383a3a566ce316c338441ed826ecf3fdcf98815/src/Controller/AdminController.php#L263-L288
                yield IdField::new('id')->setLabel('entity.report.sys_id');
                yield TextField::new('sysTitle')->setLabel('entity.report.sys_title');
                yield CollectionField::new('groups')->setLabel('entity.report.groups')
                    ->renderExpanded();
                yield TextField::new('sysOwnerSub')->setLabel('entity.report.sys_owner_sub');
                yield TextField::new('sysSystemOwner')->setLabel('entity.report.sys_system_owner');
                yield UrlField::new('sysLink')->setLabel('entity.report.sys_link')
                    ->formatValue(static fn ($value) => new TranslatableMessage('Link'));
                yield BooleanField::new('textSet')->setLabel('entity.report.text')
                    ->renderAsSwitch(false)
                    ->hideValueWhenFalse();

                return;

            case Crud::PAGE_DETAIL:
                // Cf. https://github.com/itk-dev/sysstatus/blob/5383a3a566ce316c338441ed826ecf3fdcf98815/config/packages/easy_admin.yaml#L100-L143
                yield TextField::new('sysTitle')->setLabel('entity.report.sys_title');
                yield IdField::new('sysInternalId')->setLabel('entity.report.sys_internal_id');
                yield UrlField::new('eDocUrl')->setLabel('entity.report.edoc_url');
                yield UrlField::new('sysLink')->setLabel('entity.report.sys_link');
                yield TextField::new('name')->setLabel('entity.report.name');
                yield TextEditorField::new('text')->setLabel('entity.report.text')
                    // Show raw value
                    ->setTemplatePath('admin/text_editor.raw.html.twig');
                yield TextField::new('sysSystemOwner')->setLabel('entity.report.sys_system_owner');
                // @todo Add links to each group?
                yield CollectionField::new('groups')->setLabel('entity.report.groups');
                yield CollectionField::new('answerArea')->setLabel('entity.report.answers')
                    ->setTemplatePath('easy_admin_overrides/answers_show.html.twig');
                yield TextField::new('sysAlternativeTitle')->setLabel('entity.report.sys_alternative_title');
                yield DateTimeField::new('sysUpdated')->setLabel('entity.report.sys_updated');
                yield TextField::new('sysOwner')->setLabel('entity.report.sys_owner');
                yield BooleanField::new('sysConfidentialInformation')->setLabel('entity.report.sys_confidential_information');
                yield TextField::new('sysPurpose')->setLabel('entity.report.sys_purpose');
                yield TextField::new('sysClassification')->setLabel('entity.report.sys_classification');
                yield DateTimeField::new('sysDateForRevision')->setLabel('entity.report.sys_date_for_revision');
                yield TextField::new('sysPersons')->setLabel('entity.report.sys_persons');
                yield TextField::new('sysInformationTypes')->setLabel('entity.report.sys_information_types');
                yield TextField::new('sysDataSentTo')->setLabel('entity.report.sys_data_sent_to');
                yield TextField::new('sysDataComeFrom')->setLabel('entity.report.sys_data_come_from');
                yield TextField::new('sysDataLocation')->setLabel('entity.report.sys_data_location');
                yield TextField::new('sysLatestDeletionDate')->setLabel('entity.report.sys_latest_deletion_date');
                yield TextField::new('sysDataWorthSaving')->setLabel('entity.report.sys_data_worth_saving');
                yield TextField::new('sysDataProcessors')->setLabel('entity.report.sys_data_processors');
                yield TextField::new('sysDataProcessingAgreement')->setLabel('entity.report.sys_data_processing_agreement');
                yield TextField::new('sysDataProcessingAgreementLink')->setLabel('entity.report.sys_data_processing_agreement_link');
                yield TextField::new('sysAuditorStatement')->setLabel('entity.report.sys_auditor_statement');
                yield TextField::new('sysAuditorStatementLink')->setLabel('entity.report.sys_auditor_statement_link');
                yield TextField::new('sysDataToScience')->setLabel('entity.report.sys_data_to_science');
                yield TextField::new('sysUsage')->setLabel('entity.report.sys_usage');
                yield TextField::new('sysRequestForInsight')->setLabel('entity.report.sys_request_for_insight');
                yield DateTimeField::new('sysDateUse')->setLabel('entity.report.sys_date_use');
                yield TextField::new('sysStatus')->setLabel('entity.report.sys_status');
                yield TextField::new('sysRemarks')->setLabel('entity.report.sys_remarks');
                yield TextField::new('sysInternalInformation')->setLabel('entity.report.sys_internal_information');
                yield TextField::new('sysObligationToInform')->setLabel('entity.report.sys_obligation_to_inform');
                yield TextField::new('sysLegalBasis')->setLabel('entity.report.sys_legal_basis');
                yield TextField::new('sysConsent')->setLabel('entity.report.sys_consent');
                yield TextField::new('sysImpactAnalysis')->setLabel('entity.report.sys_impact_analysis');
                yield TextField::new('sysImpactAnalysisLink')->setLabel('entity.report.sys_impact_analysis_link');
                yield TextField::new('sysAuthorizationProcedure')->setLabel('entity.report.sys_authorization_procedure');
                yield TextField::new('sysVersion')->setLabel('entity.report.sys_version');

                return;

            case Crud::PAGE_NEW:
            case Crud::PAGE_EDIT:
                // Cf. https://github.com/itk-dev/sysstatus/blob/5383a3a566ce316c338441ed826ecf3fdcf98815/config/packages/easy_admin.yaml#L144-L150
                yield TextField::new('sysTitle')->setLabel('entity.report.sys_title')
                    ->setDisabled();
                yield TextEditorField::new('text');
                yield AssociationField::new('groups')->setLabel('entity.report.groups')
                    ->setPermission('ROLE_ADMIN');
                yield UrlField::new('eDocUrl')->setLabel('entity.report.edoc_url');

                return;

            default:
                throw new \Exception('Invalid page: '.$pageName);
        }
    }
}
