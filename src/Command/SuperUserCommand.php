<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'SuperUser',
    description: 'Add a short description for your command',
)]
class SuperUserCommand extends Command
{
    private $entitManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entitManager = $entityManager;
        parent::__construct();
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
        $user = new User();
        $user->setUsername('a');
        $user->setEmail('admin@example.com');
        $user->setPassword('a');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEnabled(true);
        $this->entitManager->persist($user);
        $this->entitManager->flush();

        $io->success('you have created user:: a , with password:: a');

        return Command::SUCCESS;
    }
}
