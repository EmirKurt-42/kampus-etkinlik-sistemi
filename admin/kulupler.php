<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/header.php';
adminGerekli();

// Silme işlemi
if (isset($_GET['sil'])) {
    $stmt = $pdo->prepare("DELETE FROM clubs WHERE id = ?");
    $stmt->execute([(int)$_GET['sil']]);
    header('Location: kulupler.php');
    exit;
}

// Ekleme / güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Logo yükleme
    $logo_path = $_POST['mevcut_logo'] ?? '';
    if (!empty($_FILES['logo']['name'])) {
        $uzanti    = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo_path = uniqid() . '.' . $uzanti;
        move_uploaded_file($_FILES['logo']['tmp_name'], '../uploads/' . $logo_path);
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE clubs SET name=?, description=?, logo_path=? WHERE id=?");
        $stmt->execute([$name, $description, $logo_path, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO clubs (name, description, logo_path) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $logo_path]);
    }
    header('Location: kulupler.php');
    exit;
}

// Düzenleme
$duzenle = null;
if (isset($_GET['duzenle'])) {
    $stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
    $stmt->execute([(int)$_GET['duzenle']]);
    $duzenle = $stmt->fetch();
}

$kulupler = $pdo->query("SELECT * FROM clubs ORDER BY created_at DESC")->fetchAll();
?>

<main>
    <h2>Kulüp Yönetimi</h2>

    <h3><?= $duzenle ? 'Kulübü Düzenle' : 'Yeni Kulüp Ekle' ?></h3>

    <form method="POST" enctype="multipart/form-data">
        <?php if ($duzenle): ?>
            <input type="hidden" name="id" value="<?= $duzenle['id'] ?>">
            <input type="hidden" name="mevcut_logo" value="<?= $duzenle['logo_path'] ?>">
        <?php endif; ?>

        <label>Kulüp Adı</label>
        <input type="text" name="name" value="<?= $duzenle ? htmlspecialchars($duzenle['name']) : '' ?>" required>

        <label>Açıklama</label>
        <textarea name="description"><?= $duzenle ? htmlspecialchars($duzenle['description']) : '' ?></textarea>

        <label>Logo</label>
        <input type="file" name="logo" accept="image/*">

        <input type="submit" value="<?= $duzenle ? 'Güncelle' : 'Ekle' ?>">
        <?php if ($duzenle): ?>
            <a href="kulupler.php">İptal</a>
        <?php endif; ?>
    </form>

    <h3>Kulüpler</h3>
    <?php foreach ($kulupler as $k): ?>
    <div class="card">
        <strong><?= htmlspecialchars($k['name'] ?? '') ?></strong>
        <p><?= htmlspecialchars($k['description'] ?? '') ?></p>
        <a href="?duzenle=<?= $k['id'] ?>">Düzenle</a>
        <a href="?sil=<?= $k['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
    </div>
<?php endforeach; ?>
</main>

<?php require_once '../includes/footer.php'; ?>