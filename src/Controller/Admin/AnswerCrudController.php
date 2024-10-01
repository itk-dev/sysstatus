<?php

namespace App\Controller\Admin;

use App\DBAL\Types\SmileyType;
use App\Entity\Answer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class AnswerCrudController extends AbstractCrudController
{
    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function createEntity(string $entityFqcn)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new \Exception('HTTP request is required to create the entity.');
        }

        $questionId = $request->query->get('question');
        $reportId = $request->query->get('report');
        $systemId = $request->query->get('system');

        // check if answer already exists
        $answerRepository = $this->entityManager->getRepository(Answer::class);
        $answer = null;

        if (null === $questionId) {
            // handle this case, possibly throw an exception or return an error
            throw new \Exception('Question ID must not be null.');
        } else {
            if ($reportId) {
                $answer = $answerRepository->findOneBy(['question' => $questionId, 'report' => $reportId]);
            } elseif ($systemId) {
                $answer = $answerRepository->findOneBy(['question' => $questionId, 'system' => $systemId]);
            }
        }

        if (!$answer) {
            return new Answer();
        }

        // if answer exists return it, so it can be edited instead of creating a new entity
        return $answer;
    }

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
            return [$question, $choice, $text];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$question, $choice, $text];
        }
    }
}
