<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT e.*, c.name as club_name FROM events e 
                     JOIN clubs c ON e.club_id = c.id 
                     WHERE e.event_date >= NOW() 
                     ORDER BY e.event_date ASC LIMIT 3");
$etkinlikler = $stmt->fetchAll();
?>

<main>
    <h1>Kampüs Etkinlik & Kulüp Sistemi</h1>
    <p>Kulüpleri keşfet, etkinliklere katıl!</p>
    <a href="/kampus/pages/etkinlikler.php">Etkinliklere Göz At</a>

    <h2>Yaklaşan Etkinlikler</h2>

    <?php if (empty($etkinlikler)): ?>
        <p>Henüz etkinlik eklenmemiş.</p>
    <?php else: ?>
        <?php foreach ($etkinlikler as $e): ?>
            <div>
                <h3><?= htmlspecialchars($e['title']) ?></h3>
                <p><?= htmlspecialchars($e['club_name']) ?></p>
                <p><?= date('d.m.Y H:i', strtotime($e['event_date'])) ?></p>
                <p><?= htmlspecialchars($e['location']) ?></p>
                <a href="/kampus/pages/etkinlik-detay.php?id=<?= $e['id'] ?>">Detay</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>