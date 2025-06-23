<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Report;
use App\Entity\System;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Translation\TranslatableMessage;

class AnswerCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Answer::class;
    }

    #[\Override]
    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        // Lifted from parent::getRedirectResponseAfterSave().
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'] ?? null;

        // Answers don't have an index which is where EasyAdmin will return to by default on save.
        // Therefore we return to somewhere else on save.
        if (Action::SAVE_AND_RETURN === $submitButtonName) {
            $request = $context->getRequest();

            // Check if we have a “referer” (sic!) set.
            if ($referer = $request->get('referer')) {
                return $this->redirect($referer);
            }
            // Check if we have a “system” set.
            if ($system = $request->get('system')) {
                return $this->redirectToRoute('admin_system_detail', ['entityId' => $system]);
            }

            // Use system index as fallback.
            return $this->redirectToRoute('admin_system_index');
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    #[\Override]
    public function createEntity(string $entityFqcn): Answer
    {
        // Set question and report OR system on new answers.
        $params = $this->getContext()->getRequest()->query->all();
        // Question is required.
        $question = isset($params['question']) ? $this->entityManager->getRepository(Question::class)->find($params['question']) : null;
        if (null === $question) {
            throw new BadRequestHttpException('Missing or invalid question');
        }
        // One of report or system is required.
        $report = isset($params['report']) ? $this->entityManager->getRepository(Report::class)->find($params['report']) : null;
        $system = isset($params['system']) ? $this->entityManager->getRepository(System::class)->find($params['system']) : null;

        if (null === $report && null === $system) {
            throw new BadRequestHttpException('Either report or system must be set');
        }

        /** @var Answer $entity */
        $entity = parent::createEntity($entityFqcn);

        return $entity
            ->setQuestion($question)
            ->setReport($report)
            ->setSystem($system);
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::SAVE_AND_ADD_ANOTHER, Action::DELETE, Action::INDEX, Action::DETAIL);
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $question = TextField::new('question')
            ->setFormTypeOptions(['disabled' => true]);
        yield $question;
        yield ChoiceField::new('smiley')
            ->setTranslatableChoices([
                'GREEN' => new TranslatableMessage('smiley.GREEN'),
                'RED' => new TranslatableMessage('smiley.RED'),
                'BLUE' => new TranslatableMessage('smiley.BLUE'),
                'YELLOW' => new TranslatableMessage('smiley.YELLOW'),
            ])
            // We want to use fancy smileys in the options (cf. translations/messages.da.yml).
            ->escapeHtml(false)
        ;
        yield TextareaField::new('note');
    }
}
