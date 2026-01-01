<?php
// Update all users' roles to ensure they're properly set

require_once 'vendor/autoload.php';

$kernel = new \App\Kernel('dev', false);
$kernel->boot();

$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();
$userRepository = $em->getRepository(\App\Entity\User::class);

$users = $userRepository->findAll();

foreach ($users as $user) {
    $email = $user->getEmail();
    $roles = $user->getRoles();
    
    echo "User: $email, Current Roles: " . json_encode($roles) . "\n";
    
    // If roles are empty or only contain ROLE_USER, assign proper role
    if (empty($user->getRoles()) || $user->getRoles() === ['ROLE_USER']) {
        if (str_contains($email, 'admin')) {
            $user->setRoles(['ROLE_ADMIN']);
            echo "  -> Updated to ROLE_ADMIN\n";
        } elseif (str_contains($email, 'teacher')) {
            $user->setRoles(['ROLE_TEACHER']);
            echo "  -> Updated to ROLE_TEACHER\n";
        } else {
            $user->setRoles(['ROLE_STUDENT']);
            echo "  -> Updated to ROLE_STUDENT\n";
        }
        $em->persist($user);
    }
}

$em->flush();
echo "\nAll users updated!\n";
