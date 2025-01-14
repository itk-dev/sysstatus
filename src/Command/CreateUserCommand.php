<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create a user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly GroupRepository $groupRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password of the user, if not set, a random password will be generated')
            ->addOption('super', null, InputOption::VALUE_NONE, 'Add "super admin" user role to the user')
            ->addOption('group-id', null, InputOption::VALUE_REQUIRED, 'The ID of the group to add the user to')
            ->addOption('roles', null, (InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY), 'Roles to add to the user', ["ROLE_ADMIN"])
        ;
    }

    /**
     * @throws RandomException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');

        $password = $input->getOption('password');
        $groupId = $input->getOption('group-id');
        $roles = $input->getOption('roles');

        $super = $input->getOption('super');
        if ($super) {
            $roles[] = 'ROLE_SUPER_ADMIN';
        }

        if (!$password) {
            $password = bin2hex(random_bytes(5));
            $io->note('Password: '.$password);
        }
        $group = $this->groupRepository->find($groupId);

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(array_unique($roles));
        $user->setEnabled(true);
        $user->addGroup($group);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User have been created.');

        return Command::SUCCESS;
    }
}
