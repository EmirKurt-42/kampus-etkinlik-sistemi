<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Arama, filtreleme, sıralama
$arama   = isset($_GET['arama']) ? trim($_GET['arama']) : '';
$kulup   = isset($_GET['kulup_id']) ? (int)$_GET['kulup_id'] : 0;
$sirala  = isset($_GET['sirala']) ? $_GET['sirala'] : 'tarih';
$dolu    = isset($_GET['dolu']) ? $_GET['dolu'] : '';

$sql = "SELECT e.*, c.name as club_name,
        (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as basvuru_sayisi
        FROM events e
        JOIN clubs c ON e.club_id = c.id
        WHERE 1=1";

$params = [];

if ($arama) {
    $sql .= " AND (e.title LIKE ? OR e.location LIKE ?)";
    $params[] = "%$arama%";
    $params[] = "%$arama%";
}

if ($kulup) {
    $sql .= " AND e.club_id = ?";
    $params[] = $kulup;
}

if ($dolu === 'hayir') {
    $sql .= " AND (e.quota = 0 OR (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) < e.quota)";
}

if ($sirala === 'populer') {
    $sql .= " ORDER BY basvuru_sayisi DESC";
} else {
    $sql .= " ORDER BY e.event_date ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etkinlikler = $stmt->fetchAll();

$kulupler = $pdo->query("SELECT * FROM clubs")->fetchAll();
?>

<main>
    <h2>Etkinlikler</h2>

    <form method="GET">
        <input type="text" name="arama" placeholder="Etkinlik ara..." value="<?= htmlspecialchars($arama) ?>">
        
        <select name="kulup_id">
            <option value="">Tüm Kulüpler</option>
            <?php foreach ($kulupler as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $kulup == $k['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="dolu">
            <option value="">Tümü</option>
            <option value="hayir" <?= $dolu === 'hayir' ? 'selected' : '' ?>>Kontenjanı Dolu Olmayanlar</option>
        </select>

        <select name="sirala">
            <option value="tarih" <?= $sirala === 'tarih' ? 'selected' : '' ?>>Tarihe Göre</option>
            <option value="populer" <?= $sirala === 'populer' ? 'selected' : '' ?>>Popülerliğe Göre</option>
        </select>

        <input type="submit" value="Filtrele">
    </form>

    <?php if (empty($etkinlikler)): ?>
        <p>Etkinlik bulunamadı.</p>
    <?php else: ?>
        <?php foreach ($etkinlikler as $e): ?>
            <div class="card">
                <h3><?= htmlspecialchars($e['title']) ?></h3>
                <p>Kulüp: <?= htmlspecialchars($e['club_name']) ?></p>
                <p>Tarih: <?= date('d.m.Y H:i', strtotime($e['event_date'])) ?></p>
                <p>Konum: <?= htmlspecialchars($e['location']) ?></p>
                <p>Kontenjan: <?= $e['quota'] == 0 ? 'Sınırsız' : $e['quota'] - $e['basvuru_sayisi'] . ' yer kaldı' ?></p>
                <a href="etkinlik-detay.php?id=<?= $e['id'] ?>">Detaya Git</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>