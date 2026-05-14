<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';
girisGerekli();

$etkinlik_id = isset($_POST['etkinlik_id']) ? (int)$_POST['etkinlik_id'] : 0;

if (!$etkinlik_id) {
    header('Location: ../pages/etkinlikler.php');
    exit;
}

// Etkinlik var mı?
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$etkinlik_id]);
$etkinlik = $stmt->fetch();

if (!$etkinlik) {
    header('Location: ../pages/etkinlikler.php');
    exit;
}

// Kontenjan dolu mu?
$stmt = $pdo->prepare("SELECT COUNT(*) as sayi FROM registrations WHERE event_id = ?");
$stmt->execute([$etkinlik_id]);
$sayi = $stmt->fetch()['sayi'];

if ($etkinlik['quota'] > 0 && $sayi >= $etkinlik['quota']) {
    header('Location: ../pages/etkinlik-detay.php?id=' . $etkinlik_id . '&hata=Kontenjan doldu');
    exit;
}

// Zaten başvurmuş mu?
$stmt = $pdo->prepare("SELECT id FROM registrations WHERE event_id = ? AND user_id = ?");
$stmt->execute([$etkinlik_id, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    header('Location: ../pages/etkinlik-detay.php?id=' . $etkinlik_id . '&hata=Zaten başvurdunuz');
    exit;
}

// Başvuruyu kaydet
$stmt = $pdo->prepare("INSERT INTO registrations (event_id, user_id) VALUES (?, ?)");
$stmt->execute([$etkinlik_id, $_SESSION['user_id']]);

header('Location: ../pages/etkinlik-detay.php?id=' . $etkinlik_id . '&basari=1');
exit;