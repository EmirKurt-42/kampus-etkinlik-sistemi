<?php
require_once '../config/db.php';
require_once '../includes/header.php';
?>

<main>
    <h2>Giriş Yap</h2>

    <?php if (isset($_GET['hata'])): ?>
        <p class="hata"><?= htmlspecialchars($_GET['hata']) ?></p>
    <?php endif; ?>

    <form action="../actions/giris-isle.php" method="POST">
        <label>E-posta</label>
        <input type="email" name="email" required>

        <label>Şifre</label>
        <input type="password" name="sifre" required>

        <input type="submit" value="Giriş Yap">
    </form>

    <hr>

    <h2>Kayıt Ol</h2>

    <?php if (isset($_GET['kayit_hata'])): ?>
        <p class="hata"><?= htmlspecialchars($_GET['kayit_hata']) ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['basari'])): ?>
        <p class="basari">Kayıt başarılı! Giriş yapabilirsiniz.</p>
    <?php endif; ?>

    <form action="../actions/kayit-isle.php" method="POST">
        <label>Ad Soyad</label>
        <input type="text" name="name" required>

        <label>E-posta</label>
        <input type="email" name="email" required>

        <label>Şifre</label>
        <input type="password" name="sifre" required>

        <input type="submit" value="Kayıt Ol">
    </form>
</main>

<?php require_once '../includes/footer.php'; ?>