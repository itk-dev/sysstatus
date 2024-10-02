<?php

namespace App\Service;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReportRepository;
use App\Repository\SystemRepository;
use Doctrine\ORM\EntityManagerInterface;

class AnswerService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createOrUpdateAnswer(
        ?int $reportId,
        ?int $systemId,
        int $questionId,
        AnswerRepository $answerRepository,
        ReportRepository $reportRepository,
        SystemRepository $systemRepository,
        QuestionRepository $questionRepository,
    ): Answer {

        if (!$reportId && !$systemId) {
            throw new \Exception('Either system or report must be set', 400);
        }

        if ($reportId && $systemId) {
            throw new \Exception('System and report cannot be set at the same time', 400);
        }

        $answer = null;

        if ($reportId) {
            $answer = $answerRepository->findOneBy(['question' => $questionId, 'report' => $reportId]);

            if (!$answer) {
                $answer = new Answer();
                $answer->setQuestion($questionRepository->find($questionId));
                $answer->setReport($reportRepository->find($reportId));
                $this->entityManager->persist($answer);
            }
        }

        if ($systemId) {
            $answer = $answerRepository->findOneBy(['question' => $questionId, 'system' => $systemId]);

            if (!$answer) {
                $answer = new Answer();
                $answer->setQuestion($questionRepository->find($questionId));
                $answer->setSystem($systemRepository->find($systemId));
                $this->entityManager->persist($answer);
            }
        }

        $this->entityManager->flush();

        return $answer;
    }
}
