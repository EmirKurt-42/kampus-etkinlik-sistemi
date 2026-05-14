<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
adminGerekli();

$toplam_etkinlik = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$toplam_basvuru  = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$son7gun         = $pdo->query("SELECT COUNT(*) FROM registrations WHERE created_at >= NOW() - INTERVAL 7 DAY")->fetchColumn();

$populer = $pdo->query("SELECT e.title, COUNT(r.id) as sayi 
                        FROM events e 
                        LEFT JOIN registrations r ON e.id = r.event_id 
                        GROUP BY e.id 
                        ORDER BY sayi DESC 
                        LIMIT 1")->fetch();
?>

<main>
    <h2>Admin Paneli</h2>

    <div class="card">
        <p>Toplam Etkinlik: <strong><?php echo $toplam_etkinlik; ?></strong></p>
        <p>Toplam Başvuru: <strong><?php echo $toplam_basvuru; ?></strong></p>
        <p>Son 7 Gün Başvuru: <strong><?php echo $son7gun; ?></strong></p>
        <p>En Popüler Etkinlik: 
            <strong>
                <?php if ($populer): ?>
                    <?php echo htmlspecialchars($populer['title']); ?> (<?php echo $populer['sayi']; ?> başvuru)
                <?php else: ?>
                    Henüz yok
                <?php endif; ?>
            </strong>
        </p>
    </div>

    <h3>Yönetim</h3>
    <a href="kulupler.php" class="btn">Kulüp Yönetimi</a>
    <a href="etkinlikler.php" class="btn">Etkinlik Yönetimi</a>
    <a href="basvurular.php" class="btn">Başvurular</a>
</main>

<?php require_once '../includes/footer.php'; ?>