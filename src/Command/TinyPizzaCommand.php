<?php

namespace App\Command;

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
        $group = new Group();
        $group->setName('My awesome Group');
        $group->setRoles(['ROLE_USER']);
        $this->entityManager->persist($group);
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

        $theme = new Theme();
        $theme->setName('My awesome Theme');
        $theme->addSystemGroup($group);

        $themecategory = new ThemeCategory();
        $themecategory->setTheme($theme);
        $themecategory->setCategory($category);
        $themecategory->setSortOrder(0);
        $theme->addThemeCategory($themecategory);
        $this->entityManager->persist($theme);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
