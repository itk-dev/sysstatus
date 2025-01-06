<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnswerController extends AbstractController
{
    #[Route('/answer/new', name: 'report')]
    public function newAnswer(
        Request $request,
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        QuestionRepository $questionRepository,
        AnswerRepository $answerRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $reportId = $request->query->get('report');
        $systemId = $request->query->get('system');
        $questionId = $request->query->get('question');

        if (!isset($questionId)) {
            throw new \Exception('question not found', 400);
        }

        if (!isset($reportId) && !isset($systemId)) {
            throw new \Exception('system or report should be set', 400);
        }

        if (isset($reportId) && isset($systemId)) {
            throw new \Exception('system and report should be set at the same time', 400);
        }

        if (isset($reportId)) {
            $answer = $answerRepository->findOneBy(
                ['question' => $questionId, 'report' => $reportId]
            );

            if (!$answer) {
                $answer = new Answer();
                $answer->setQuestion($questionRepository->find($questionId));
                $answer->setReport($reportRepository->find($reportId));
                $entityManager->persist($answer);
            }
        }
        if (isset($systemId)) {
            $answer = $answerRepository->findOneBy(
                ['question' => $questionId, 'system' => $systemId]
            );

            if (!$answer) {
                $answer = new Answer();
                $answer->setQuestion($questionRepository->find($questionId));
                $answer->setSystem($systemRepository->find($systemId));
                $entityManager->persist($answer);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute(
            'app_admin_customdashboardcrud_index',
            [
                'crudAction' => 'edit',
                'entityId' => $answer->getId(),
                'referer' => $request->query->get('referer'),
                'crudControllerFqcn' => AnswerCrudController::class,

            ]
        );
    }
}
