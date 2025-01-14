<?php

namespace App\Command;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
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
    name: 'app:user:update ',
    description: 'Update user',
)]
class UpdateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly GroupRepository $groupRepository,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user to update')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password of the user, if not set, a random password will be generated')
            ->addOption('generate-password', null, InputOption::VALUE_NONE, 'Generate a random password for the user')
            ->addOption('super', null, InputOption::VALUE_NONE, 'Add "super admin" user role to the user')
            ->addOption('group-id', null, InputOption::VALUE_REQUIRED, 'The ID of the group to add the user to')
            ->addOption('roles', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Roles to add to the user')
            ->addOption('enable', null, InputOption::VALUE_NONE, 'Enable the user')
            ->addOption('disable', null, InputOption::VALUE_NONE, 'Disable the user')
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
        $generatePassword = $input->getOption('generate-password');

        $groupId = $input->getOption('group-id');
        $roles = $input->getOption('roles');

        $enable = $input->getOption('enable');
        $disable = $input->getOption('disable');

        $super = $input->getOption('super');
        if ($super) {
            $roles[] = 'ROLE_SUPER_ADMIN';
        }

        if ($generatePassword) {
            if (null !== $password) {
                $io->info('Random password will be generated. As you can not use supplied password with --generate-password in same command');
            }
            $password = bin2hex(random_bytes(5));
            $io->note('Password: '.$password);
        }

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if ($email) {
            $user->setEmail($email);
        }

        if ($password || $generatePassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        }

        if ($disable) {
            $user->setEnabled(false);
        }
        if ($enable) {
            $user->setEnabled(true);
        }

        if (!empty($roles)) {
            $user->setRoles(array_unique($roles));
        }

        if ($groupId) {
            $group = $this->groupRepository->find($groupId);
            $user->addGroup($group);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User have been updated.');

        return Command::SUCCESS;
    }
}
