<?php
$hash = password_hash('password', PASSWORD_BCRYPT);

$pdo = new PDO(
    'mysql:host=mariadb;port=3306;dbname=ccsecurity_db',
    'root',
    'password',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$stmt = $pdo->prepare("UPDATE super_admins SET password = :pw WHERE email = 'insane0225@gmail.com'");
$stmt->execute([':pw' => $hash]);

echo "Rows affected: " . $stmt->rowCount() . "\n";

$stmt = $pdo->prepare("SELECT password FROM super_admins WHERE email = 'insane0225@gmail.com'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$stored = $row['password'];
echo "Hash: " . $stored . "\n";
echo "Verify: " . (password_verify('password', $stored) ? 'OK' : 'FAIL') . "\n";
