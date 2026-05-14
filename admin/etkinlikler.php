<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
adminGerekli();

// Silme işlemi
if (isset($_GET['sil'])) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([(int)$_GET['sil']]);
    header('Location: etkinlikler.php');
    exit;
}

// Ekleme / güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_id     = (int)$_POST['club_id'];
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = $_POST['event_date'];
    $location    = trim($_POST['location']);
    $quota       = (int)$_POST['quota'];
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Afiş yükleme
    $poster_path = $_POST['mevcut_poster'] ?? '';
    if (!empty($_FILES['poster']['name'])) {
        $uzanti      = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $poster_path = uniqid() . '.' . $uzanti;
        move_uploaded_file($_FILES['poster']['tmp_name'], '../uploads/' . $poster_path);
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE events SET club_id=?, title=?, description=?, event_date=?, location=?, quota=?, poster_path=? WHERE id=?");
        $stmt->execute([$club_id, $title, $description, $event_date, $location, $quota, $poster_path, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (club_id, title, description, event_date, location, quota, poster_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$club_id, $title, $description, $event_date, $location, $quota, $poster_path]);
    }
    header('Location: etkinlikler.php');
    exit;
}

// Düzenleme
$duzenle = null;
if (isset($_GET['duzenle'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([(int)$_GET['duzenle']]);
    $duzenle = $stmt->fetch();
}

$etkinlikler = $pdo->query("SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id ORDER BY e.event_date DESC")->fetchAll();
$kulupler = $pdo->query("SELECT id, name FROM clubs ORDER BY name")->fetchAll();
?>

<main>
    <h2>Etkinlik Yönetimi</h2>

    <h3><?= $duzenle ? 'Etkinliği Düzenle' : 'Yeni Etkinlik Ekle' ?></h3>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($duzenle): ?>
            <input type="hidden" name="id" value="<?= $duzenle['id'] ?>">
            <input type="hidden" name="mevcut_poster" value="<?= $duzenle['poster_path'] ?>">
        <?php endif; ?>

        <label>Kulüp</label>
        <select name="club_id" required>
    <?php foreach ($kulupler as $k): ?>
        <option value="<?= $k['id'] ?>" <?= $duzenle && $duzenle['club_id'] == $k['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['name'] ?? '') ?>
        </option>
    <?php endforeach; ?>
</select>

        <label>Başlık</label>
        <input type="text" name="title" value="<?= $duzenle ? htmlspecialchars($duzenle['title']) : '' ?>" required>

        <label>Açıklama</label>
        <textarea name="description"><?= $duzenle ? htmlspecialchars($duzenle['description']) : '' ?></textarea>

        <label>Tarih</label>
        <input type="datetime-local" name="event_date" value="<?= $duzenle ? date('Y-m-d\TH:i', strtotime($duzenle['event_date'])) : '' ?>" required>

        <label>Konum</label>
        <input type="text" name="location" value="<?= $duzenle ? htmlspecialchars($duzenle['location']) : '' ?>">

        <label>Kontenjan (0 = sınırsız)</label>
        <input type="number" name="quota" value="<?= $duzenle ? $duzenle['quota'] : '0' ?>" min="0">

        <label>Afiş</label>
        <input type="file" name="poster" accept="image/*">

        <input type="submit" value="<?= $duzenle ? 'Güncelle' : 'Ekle' ?>">
        <?php if ($duzenle): ?>
            <a href="etkinlikler.php">İptal</a>
        <?php endif; ?>
    </form>

    <h3>Etkinlikler</h3>
    <?php foreach ($etkinlikler as $e): ?>
    <div class="card">
        <strong><?= htmlspecialchars($e['title'] ?? '') ?></strong>
        <p>Kulüp: <?= htmlspecialchars($e['club_name'] ?? '') ?></p>
        <p>Tarih: <?= date('d.m.Y H:i', strtotime($e['event_date'])) ?></p>
        <a href="?duzenle=<?= $e['id'] ?>">Düzenle</a>
        <a href="?sil=<?= $e['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
    </div>
<?php endforeach; ?>
</main>

<?php require_once '../includes/footer.php'; ?>