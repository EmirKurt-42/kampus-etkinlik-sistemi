<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
$stmt->execute([$id]);
$kulup = $stmt->fetch();

if (!$kulup) {
    echo "<main><p>Kulüp bulunamadı.</p></main>";
    require_once '../includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE club_id = ? ORDER BY event_date ASC");
$stmt->execute([$id]);
$etkinlikler = $stmt->fetchAll();
?>

<main>
    <h2><?= htmlspecialchars($kulup['name'] ?? '') ?></h2>
    <p><?= htmlspecialchars($kulup['description'] ?? '') ?></p>
    <h3>Bu Kulübün Etkinlikleri</h3>

    <?php if (empty($etkinlikler)): ?>
        <p>Bu kulübün henüz etkinliği yok.</p>
    <?php else: ?>
        <?php foreach ($etkinlikler as $e): ?>
            <div class="card">
                <h4><?= htmlspecialchars($e['title']) ?></h4>
                <p>Tarih: <?= date('d.m.Y H:i', strtotime($e['event_date'])) ?></p>
                <p>Konum: <?= htmlspecialchars($e['location']) ?></p>
                <a href="etkinlik-detay.php?id=<?= $e['id'] ?>">Detaya Git</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>