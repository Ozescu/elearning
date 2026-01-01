<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env when running scripts (so DATABASE_URL is available)
if (class_exists(Dotenv::class)) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$defaults = [
    ['email' => 'teacher@example.com', 'password' => 'TeacherPass123', 'roles' => ['ROLE_TEACHER']],
    ['email' => 'student@example.com', 'password' => 'StudentPass123', 'roles' => ['ROLE_STUDENT']],
];

// Optional: allow passing custom values as args: php create_users.php teacher@example.com pass student@example.com pass
if ($argc === 5) {
    $defaults = [
        ['email' => $argv[1], 'password' => $argv[2], 'roles' => ['ROLE_TEACHER']],
        ['email' => $argv[3], 'password' => $argv[4], 'roles' => ['ROLE_STUDENT']],
    ];
}

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();
$conn = $container->get('doctrine')->getConnection();

foreach ($defaults as $u) {
    $email = $u['email'];
    $plain = $u['password'];
    $rolesJson = json_encode($u['roles']);
    $hash = password_hash($plain, PASSWORD_BCRYPT);
    $now = (new \DateTime())->format('Y-m-d H:i:s');

    $existing = $conn->fetchAssociative('SELECT id FROM `user` WHERE email = :email', ['email' => $email]);
    if ($existing) {
        $id = $existing['id'];
        $sql = 'UPDATE `user` SET `password` = :hash, `roles` = :roles, `is_approved` = :approved WHERE id = :id';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('hash', $hash);
        $stmt->bindValue('roles', $rolesJson);
        $stmt->bindValue('approved', 1);
        $stmt->bindValue('id', $id);
        $stmt->executeStatement();
        echo "Updated user $email (id=$id)\n";
    } else {
        $sql = 'INSERT INTO `user` (`email`, `roles`, `password`, `is_approved`, `created_at`) VALUES (:email, :roles, :hash, :approved, :created_at)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->bindValue('roles', $rolesJson);
        $stmt->bindValue('hash', $hash);
        $stmt->bindValue('approved', 1);
        $stmt->bindValue('created_at', $now);
        $stmt->executeStatement();
        $id = $conn->lastInsertId();
        echo "Created user $email (id=$id)\n";
    }

    // verify
    $row = $conn->fetchAssociative('SELECT password FROM `user` WHERE email = :email', ['email' => $email]);
    if ($row && password_verify($plain, $row['password'])) {
        echo "Password verified for $email\n";
    } else {
        echo "Password verification failed for $email\n";
    }
}

echo "Done.\n";
