<?php
// Usage: php set_password_pdo.php email plainpassword
if ($argc < 3) {
    echo "Usage: php set_password_pdo.php email plainpassword\n";
    exit(1);
}
$email = $argv[1];
$plain = $argv[2];
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=elearndb;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $hash = password_hash($plain, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE `user` SET `password` = :hash WHERE `email` = :email');
    $stmt->execute(['hash' => $hash, 'email' => $email]);
    $stmt = $pdo->prepare('SELECT `password` FROM `user` WHERE `email` = :email');
    $stmt->execute(['email' => $email]);
    $stored = $stmt->fetchColumn();
    if ($stored && password_verify($plain, $stored)) {
        echo "Password updated and verified for $email\n";
        exit(0);
    }
    echo "Password update or verification failed for $email\n";
    exit(2);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(3);
}
