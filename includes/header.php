<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kampüs Etkinlik Sistemi</title>
    <link rel="stylesheet" href="/kampus/assets/css/style.css">
    <script src="/kampus/assets/js/main.js"></script>
</head>
<body>
<nav>
    <nav>
    <a href="/kampus/index.php">Ana Sayfa</a>
    <a href="/kampus/pages/kulupler.php">Kulüpler</a>
    <a href="/kampus/pages/etkinlikler.php">Etkinlikler</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/kampus/pages/profil.php">Profilim</a>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="/kampus/admin/index.php">Admin Panel</a>
        <?php endif; ?>
        <a href="/kampus/actions/cikis.php">Çıkış</a>
    <?php else: ?>
        <a href="/kampus/pages/giris.php">Giriş / Kayıt</a>
    <?php endif; ?>
</nav>
</nav>