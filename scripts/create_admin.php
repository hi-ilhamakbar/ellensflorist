<?php
// Run once from a protected CLI or browser, then delete this file.
require dirname(__DIR__) . '/app/bootstrap.php';
$users = [['nayatan','Nayatan93!','superadmin'], ['gdx','SiTampan100%','superadmin']];
$stmt = db()->prepare('INSERT INTO users (name, username, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
foreach ($users as [$username, $password, $role]) {
    $stmt->execute([$username, $username, $username . '@ellensflorist.com', password_hash($password, PASSWORD_DEFAULT), $role]);
}
echo 'Accounts created. Delete scripts/create_admin.php immediately.';
