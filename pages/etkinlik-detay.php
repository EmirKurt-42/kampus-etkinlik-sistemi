<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT e.*, c.name as club_name,
                       (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as basvuru_sayisi
                       FROM events e
                       JOIN clubs c ON e.club_id = c.id
                       WHERE e.id = ?");
$stmt->execute([$id]);
$etkinlik = $stmt->fetch();

if (!$etkinlik) {
    echo "<main><p>Etkinlik bulunamadı.</p></main>";
    require_once '../includes/footer.php';
    exit;
}

$kontenjan_dolu = $etkinlik['quota'] > 0 && $etkinlik['basvuru_sayisi'] >= $etkinlik['quota'];

$zaten_basvurdu = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $zaten_basvurdu = $stmt->fetch() ? true : false;
}
?>

<main>
    <?php if ($etkinlik['poster_path']): ?>
        <img src="/kampus/uploads/<?= htmlspecialchars($etkinlik['poster_path']) ?>" width="300">
    <?php endif; ?>

    <h2><?= htmlspecialchars($etkinlik['title']) ?></h2>
    <p>Kulüp: <?= htmlspecialchars($etkinlik['club_name']) ?></p>
    <p>Tarih: <?= date('d.m.Y H:i', strtotime($etkinlik['event_date'])) ?></p>
    <p>Konum: <?= htmlspecialchars($etkinlik['location']) ?></p>
    <p>Kontenjan: <?= $etkinlik['quota'] == 0 ? 'Sınırsız' : $etkinlik['quota'] - $etkinlik['basvuru_sayisi'] . ' yer kaldı' ?></p>
    <p><?= htmlspecialchars($etkinlik['description']) ?></p>

    <?php if (isset($_GET['basari'])): ?>
        <p class="basari">Başvurunuz alındı!</p>
    <?php endif; ?>

    <?php if (isset($_GET['hata'])): ?>
        <p class="hata"><?= htmlspecialchars($_GET['hata']) ?></p>
    <?php endif; ?>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <p>Başvurmak için <a href="giris.php">giriş yapın</a>.</p>
    <?php elseif ($zaten_basvurdu): ?>
        <p class="basari">Bu etkinliğe zaten başvurdunuz.</p>
    <?php elseif ($kontenjan_dolu): ?>
        <p class="hata">Kontenjan doldu, başvuru alınmıyor.</p>
    <?php else: ?>
        <form action="../actions/basvuru-isle.php" method="POST">
            <input type="hidden" name="etkinlik_id" value="<?= $etkinlik['id'] ?>">
            <input type="submit" value="Etkinliğe Başvur">
        </form>
    <?php endif; ?>

    <h3>Etkinlik QR Kodu</h3>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://localhost/kampus/pages/etkinlik-detay.php?id=<?= $etkinlik['id'] ?>" alt="QR Kod">
    <p>QR kodu okutarak etkinlik sayfasına ulaşabilirsiniz.</p>
</main>

<?php require_once '../includes/footer.php'; ?>