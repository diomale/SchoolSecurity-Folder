<?php
$hash = password_hash('password', PASSWORD_BCRYPT);
echo $hash . "\n";

$pdo = new PDO('mysql:host=mariadb;port=3306;dbname=ccsecurity_db', 'root', 'password');
$stmt = $pdo->prepare("UPDATE super_admins SET password = ? WHERE email = 'insane0225@gmail.com'");
$stmt->execute([$hash]);
echo "Updated: " . $stmt->rowCount() . " rows\n";

// Verify
$stmt = $pdo->prepare("SELECT password FROM super_admins WHERE email = 'insane0225@gmail.com'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Stored hash: " . $row['password'] . "\n";
echo "Verify: " . (password_verify('password', $row['password']) ? "OK" : "FAIL") . "\n";
