<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$stmt = $pdo->query("SELECT * FROM clubs ORDER BY created_at DESC");
$kulupler = $stmt->fetchAll();
?>

<main>
    <h2>Kulüpler</h2>

    <?php if (empty($kulupler)): ?>
        <p>Henüz kulüp eklenmemiş.</p>
    <?php else: ?>
        <?php foreach ($kulupler as $k): ?>
    <div class="card">
        <?php if (!empty($k['logo_path'])): ?>
            <img src="/kampus/uploads/<?= htmlspecialchars($k['logo_path'] ?? '') ?>" width="80">
        <?php endif; ?>
        <h3><?= htmlspecialchars($k['name'] ?? '') ?></h3>
        <p><?= htmlspecialchars($k['description'] ?? '') ?></p>
        <a href="kulup-detay.php?id=<?= $k['id'] ?>">Detaya Git</a>
    </div>
<?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>