<?php
error_reporting(0);
require_once '../config/db.php';
require_once '../includes/auth.php';
adminGerekli();

// CSV indirme
if (isset($_GET['csv']) && isset($_GET['etkinlik_id'])) {
    $eid = (int)$_GET['etkinlik_id'];
    $stmt = $pdo->prepare("SELECT u.name, u.email, r.status, r.created_at 
                           FROM registrations r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.event_id = ?");
    $stmt->execute([$eid]);
    $satirlar = $stmt->fetchAll();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="basvurular.csv"');

    $cikti = fopen('php://output', 'w');
    fprintf($cikti, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($cikti, ['Ad Soyad', 'E-posta', 'Durum', 'Başvuru Tarihi']);

    $durumlar = ['pending' => 'Beklemede', 'approved' => 'Onaylandı', 'cancelled' => 'İptal Edildi'];
    foreach ($satirlar as $s) {
        $durum = $durumlar[$s['status'] ?? 'pending'] ?? 'Beklemede';
        fputcsv($cikti, [$s['name'], $s['email'], $durum, $s['created_at']]);
    }
    fclose($cikti);
    exit;
}

require_once '../includes/header.php';

$etkinlikler = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$etkinlik_id = isset($_GET['etkinlik_id']) ? (int)$_GET['etkinlik_id'] : 0;

$basvurular = [];
if ($etkinlik_id) {
    $stmt = $pdo->prepare("SELECT r.*, u.name, u.email 
                           FROM registrations r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.event_id = ? 
                           ORDER BY r.created_at ASC");
    $stmt->execute([$etkinlik_id]);
    $basvurular = $stmt->fetchAll();
}
?>

<main>
    <h2>Başvuru Listesi</h2>

    <form method="GET">
        <select name="etkinlik_id">
            <option value="">Etkinlik Seç</option>
            <?php foreach ($etkinlikler as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $etkinlik_id == $e['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e['title'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Listele">
    </form>

    <?php if ($etkinlik_id && empty($basvurular)): ?>
        <p>Bu etkinliğe henüz başvuru yok.</p>
    <?php endif; ?>

    <?php foreach ($basvurular as $b): ?>
        <div class="card">
            <p>Ad: <?= htmlspecialchars($b['name'] ?? '') ?></p>
            <p>E-posta: <?= htmlspecialchars($b['email'] ?? '') ?></p>
            <?php
            $durumlar = ['pending' => 'Beklemede', 'approved' => 'Onaylandı', 'cancelled' => 'İptal Edildi'];
            $durum = $b['status'] ?? 'pending';
            ?>
            <p>Durum: <?= $durumlar[$durum] ?? 'Beklemede' ?></p>
            <p>Tarih: <?= date('d.m.Y H:i', strtotime($b['created_at'])) ?></p>
        </div>
    <?php endforeach; ?>

    <?php if ($etkinlik_id && !empty($basvurular)): ?>
        <a href="?etkinlik_id=<?= $etkinlik_id ?>&csv=1" class="btn">CSV İndir</a>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>