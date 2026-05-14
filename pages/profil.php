<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
girisGerekli();

$stmt = $pdo->prepare("SELECT r.id, r.status, e.title, e.event_date, e.location 
                       FROM registrations r 
                       JOIN events e ON r.event_id = e.id 
                       WHERE r.user_id = ? 
                       ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$basvurular = $stmt->fetchAll();

$durumlar = [
    'pending'   => 'Beklemede',
    'approved'  => 'Onaylandı',
    'cancelled' => 'İptal Edildi'
];
?>

<main>
    <h2>Profilim</h2>
    <p>Hoş geldin, <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>

    <h3>Başvurularım</h3>

    <?php if (empty($basvurular)): ?>
        <p>Henüz hiçbir etkinliğe başvurmadınız.</p>
    <?php else: ?>
        <?php foreach ($basvurular as $b): ?>
            <div class="card">
                <h4><?= htmlspecialchars($b['title']) ?></h4>
                <p>Tarih: <?= date('d.m.Y H:i', strtotime($b['event_date'])) ?></p>
                <p>Konum: <?= htmlspecialchars($b['location']) ?></p>
                <p>Durum: <?= isset($durumlar[$b['status']]) ? $durumlar[$b['status']] : 'Beklemede' ?></p>
                <a href="../actions/basvuru-iptal.php?id=<?= $b['id'] ?>" 
                   onclick="return confirm('İptal etmek istediğinize emin misiniz?')">İptal Et</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>