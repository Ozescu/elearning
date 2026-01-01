<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fix-user-roles',
    description: 'Fix user roles that are empty or missing',
)]
class FixUserRolesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepository = $this->em->getRepository(User::class);
        $users = $userRepository->findAll();

        $output->writeln('Fixing user roles...');

        foreach ($users as $user) {
            $email = $user->getEmail();
            $roles = $user->getRoles();
            
            $output->writeln("User: $email, Current Roles: " . json_encode($roles));
            
            // If roles don't contain a specific role (only ROLE_USER from getRoles()),
            // assign proper role based on email
            if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_TEACHER', $roles) && !in_array('ROLE_STUDENT', $roles)) {
                if (str_contains($email, 'admin')) {
                    $user->setRoles(['ROLE_ADMIN']);
                    $output->writeln("  -> Updated to ROLE_ADMIN");
                } elseif (str_contains($email, 'teacher')) {
                    $user->setRoles(['ROLE_TEACHER']);
                    $output->writeln("  -> Updated to ROLE_TEACHER");
                } else {
                    $user->setRoles(['ROLE_STUDENT']);
                    $output->writeln("  -> Updated to ROLE_STUDENT");
                }
                $this->em->persist($user);
            }
        }

        $this->em->flush();
        $output->writeln('All users updated!');
        
        return Command::SUCCESS;
    }
}
