<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
girisGerekli();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: ../pages/profil.php');
    exit;
}

// Sadece kendi başvurusunu iptal edebilir
$stmt = $pdo->prepare("DELETE FROM registrations WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

header('Location: ../pages/profil.php');
exit;