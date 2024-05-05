<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'create-user',
    description: 'Creates a default login user',
)]
class CreateUserCommand extends Command
{
    private mixed $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setUsername('admin');
        $encodedPassword = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($encodedPassword);
        $user->setEmail('admin@admin.com');
        $user->setBatchId(base64_encode(random_bytes(10)));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln('User created successfully!');

        return Command::SUCCESS;
    }
}
