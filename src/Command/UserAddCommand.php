<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'user:add')]
class UserAddCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username       = $input->getArgument('username');
        $user           = new User();
        $pass           = User::generatePassword(16);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $pass
        );

        $user
            ->setEmail($username)
            ->setPassword($hashedPassword)
            ->setRoles([User::ROLE_ADMIN])
            ->setPassword($hashedPassword)
        ;

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<info>User %s:%s created successfully</info>', $username, $pass));

        return Command::SUCCESS;
    }
}
