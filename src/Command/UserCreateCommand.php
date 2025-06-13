<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\ByteString;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create user',
)]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password')
            ->addOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The roles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('Invalid email: %s', $email));
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user) {
            throw new InvalidArgumentException(sprintf('User with email %s already exists', $email));
        }

        $roles = array_unique([
            ...(array) $input->getOption('role'),
            'ROLE_USER',
        ]);

        $password = $input->getOption('password') ?? ByteString::fromRandom(12);

        $user = new User();
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles($roles);
        $user->setEnabled(true);
        $this->userRepository->save($user, true);

        $io->success(sprintf('User %s (%s) created with password %s and roles %s', $user->getUsername(), $user->getEmail(), $password, implode(', ', $user->getRoles())));

        return Command::SUCCESS;
    }
}
