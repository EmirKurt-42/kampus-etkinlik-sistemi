<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    if (empty($email) || empty($sifre)) {
        header('Location: ../pages/giris.php?hata=Tüm alanları doldurun');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($sifre, $user['password_hash'])) {
        header('Location: ../pages/giris.php?hata=E-posta veya şifre hatalı');
        exit;
    }

    session_start();
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}