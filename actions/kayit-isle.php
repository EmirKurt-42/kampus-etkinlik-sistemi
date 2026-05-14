<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    // PHP tarafı doğrulama
    if (empty($name) || empty($email) || empty($sifre)) {
        header('Location: ../pages/giris.php?kayit_hata=Tüm alanları doldurun');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../pages/giris.php?kayit_hata=Geçersiz e-posta');
        exit;
    }

    if (strlen($sifre) < 6) {
        header('Location: ../pages/giris.php?kayit_hata=Şifre en az 6 karakter olmalı');
        exit;
    }

    // E-posta daha önce alınmış mı?
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: ../pages/giris.php?kayit_hata=Bu e-posta zaten kayıtlı');
        exit;
    }

    // Şifreyi hashle ve kaydet
    $hash = password_hash($sifre, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);

    header('Location: ../pages/giris.php?basari=1');
    exit;
}