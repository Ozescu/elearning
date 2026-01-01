<?php

use App\Kernel;

require __DIR__ . '/../vendor/autoload.php';

if ($argc < 3) {
    echo "Usage: php set_password.php user@example.com newpassword\n";
    exit(1);
}

$email = $argv[1];
$plain = $argv[2];

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();

// generate bcrypt hash
$hash = password_hash($plain, PASSWORD_BCRYPT);

$conn = $container->get('doctrine')->getConnection();

$sql = 'UPDATE `user` SET `password` = :hash WHERE `email` = :email';
$stmt = $conn->prepare($sql);
$stmt->bindValue('hash', $hash);
$stmt->bindValue('email', $email);
$stmt->executeStatement();

// verify
$row = $conn->fetchAssociative('SELECT password FROM `user` WHERE email = :email', ['email' => $email]);

if (! $row) {
    echo "User not found: $email\n";
    exit(1);
}

$stored = $row['password'];

if (password_verify($plain, $stored)) {
    echo "Password updated and verified for $email\n";
    exit(0);
}

echo "Password update failed or verification failed for $email\n";
exit(2);
