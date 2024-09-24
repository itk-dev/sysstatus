<?php

namespace App\Command;

use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\Group;
use App\Entity\Question;
use App\Entity\System;
use App\Entity\Theme;
use App\Entity\ThemeCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tiny-pizza',
    description: 'Add a short description for your command',
)]
class TinyPizzaCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // # 1 Create a Group
        $group = new Group();
        $group->setName('My awesome Group');
        $group->setRoles(['ROLE_USER']);
        $this->entityManager->persist($group);
        // # 2 Create a Category with 2 questions
        $category = new Category();
        $category->setName('My awesome Category');
        $question1 = new Question();
        $question1->setQuestion('What is your favorite color?');
        $question1->setSortOrder(0);
        $question2 = new Question();
        $question2->setQuestion('What is your least favorite color?');
        $question2->setSortOrder(1);
        $category->addQuestion($question1);
        $category->addQuestion($question2);
        $this->entityManager->persist($category);

        // # 3 Through Theme link System to the Group, and the Category with 2 question
        $theme = new Theme();
        $theme->setName('My awesome Theme');
        $theme->addSystemGroup($group);

        $themecategory = new ThemeCategory();
        $themecategory->setTheme($theme);
        $themecategory->setCategory($category);
        $themecategory->setSortOrder(0);
        $theme->addThemeCategory($themecategory);
        $this->entityManager->persist($theme);
        $this->entityManager->flush(); // # flush to save

        // # 4 Find the system 'Systemportalen' and link it to the Group
        $systemportalen = $this->entityManager->find(System::class, 2);
        if (!$systemportalen) {
            $io->error('No System entity found with id=2');

            return Command::FAILURE;
        }
        $group = $this->entityManager->getRepository(Group::class)->findOneBy(['name' => 'My awesome Group']);
        if (!$group) {
            $io->error("No Group entity found with name 'My awesome Group'");

            return Command::FAILURE;
        }
        $systemportalen->addGroup($group);
        $this->entityManager->persist($systemportalen);
        $this->entityManager->flush(); // # flush to save

        // 5 Finding system portal.
        // Creating new smiley and note in entity Answer
        // putting question with id 1, into answer.
        // puting answer into systemportal
        $systemportalen = $this->entityManager->find(System::class, 2);
        if (!$systemportalen) {
            $io->error('No System entity found with id=2');

            return Command::FAILURE;
        }

        $answer = new Answer();
        $answer->setSmiley('BLUE');
        $answer->setNote('I am an awesome note');

        // Fetch a Question instance
        $questionRepository = $this->entityManager->getRepository(Question::class);
        $question = $questionRepository->find(1);

        if (!$question) {
            $io->error('Question with id 1 > What is your favourite color, was not found');

            return Command::FAILURE;
        }

        $answer->setQuestion($question);
        $this->entityManager->persist($answer);
        $systemportalen->addAnswer($answer);
        $this->entityManager->persist($systemportalen);
        $this->entityManager->flush(); // flush to save

        return Command::SUCCESS;
    }
}
