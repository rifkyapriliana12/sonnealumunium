<?php
require_once 'config.php';
requireLogin();
$pdo = getDB();
$flash = getFlash();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = $_POST;
    $logo = $postData['existing_logo'] ?? '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) {
            $newFilename = 'partner-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/partners/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $newFilename)) $logo = $newFilename;
        }
    }
    if ($postData['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO partners (name, logo, url, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([sanitize($postData['name']), $logo, sanitize($postData['url']), intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0]);
        setFlash('Partner berhasil ditambahkan!'); header('Location: partners.php'); exit;
    } elseif ($postData['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE partners SET name=?, logo=?, url=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([sanitize($postData['name']), $logo, sanitize($postData['url']), intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0, intval($postData['id'])]);
        setFlash('Partner berhasil diupdate!'); header('Location: partners.php'); exit;
    }
}
if ($action == 'delete' && $id) { $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?"); $stmt->execute([intval($id)]); setFlash('Partner berhasil dihapus!', 'error'); header('Location: partners.php'); exit; }
$partner = null;
if ($action == 'edit' && $id) { $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?"); $stmt->execute([intval($id)]); $partner = $stmt->fetch(); }
$partners = $pdo->query("SELECT * FROM partners ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Partner - Admin Sonne Aluminium</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header"><img src="https://sonnealuminium.com/wp-content/uploads/2025/07/LOGO-SONNE-2025-FINAL-GOLD-980x319.png" alt="Logo"></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="sliders.php" class="nav-item"><i class="fas fa-images"></i><span>Slider</span></a>
            <a href="products.php" class="nav-item"><i class="fas fa-box"></i><span>Produk</span></a>
            <a href="projects.php" class="nav-item"><i class="fas fa-building"></i><span>Proyek</span></a>
            <a href="partners.php" class="nav-item active"><i class="fas fa-handshake"></i><span>Partner</span></a>
            <a href="blog.php" class="nav-item"><i class="fas fa-blog"></i><span>Blog</span></a>
            <a href="testimonials.php" class="nav-item"><i class="fas fa-quote-right"></i><span>Testimoni</span></a>
            <a href="media.php" class="nav-item"><i class="fas fa-photo-video"></i><span>Media</span></a>
            <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i><span>Pengaturan</span></a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.html" target="_blank" class="nav-item"><i class="fas fa-external-link-alt"></i><span>Lihat Website</span></a>
            <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Keluar</span></a>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar"><button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button><div class="topbar-right"><span class="admin-name"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['admin_name']; ?></span></div></header>
        <div class="content">
            <?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div><?php endif; ?>
            <?php if ($action == 'list'): ?>
                <div class="table-container">
                    <div class="table-header"><h2>Kelola Partner</h2><a href="partners.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Partner</a></div>
                    <?php if (empty($partners)): ?>
                        <div class="empty-state"><i class="fas fa-handshake"></i><p>Belum ada partner</p><a href="partners.php?action=add" class="btn btn-primary">Tambah Partner Pertama</a></div>
                    <?php else: ?>
                        <table>
                            <thead><tr><th>Logo</th><th>Nama</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php foreach ($partners as $p): ?>
                                    <tr>
                                        <td><?php if ($p['logo']): ?><img src="../uploads/partners/<?php echo $p['logo']; ?>" class="table-img" alt=""><?php else: ?><span>-</span><?php endif; ?></td>
                                        <td><?php echo $p['name']; ?></td>
                                        <td><?php echo $p['sort_order']; ?></td>
                                        <td><span class="status-badge <?php echo $p['is_active'] ? 'status-active' : 'status-inactive'; ?>"><?php echo $p['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                        <td><div class="table-actions">
                                            <a href="partners.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="partners.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php elseif ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <h2><?php echo $action == 'add' ? 'Tambah Partner' : 'Edit Partner'; ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($partner): ?><input type="hidden" name="id" value="<?php echo $partner['id']; ?>"><input type="hidden" name="existing_logo" value="<?php echo $partner['logo']; ?>"><?php endif; ?>
                        <div class="form-group"><label>Nama Partner *</label><input type="text" name="name" required value="<?php echo $partner['name'] ?? ''; ?>"></div>
                        <div class="form-group"><label>URL Website</label><input type="text" name="url" value="<?php echo $partner['url'] ?? ''; ?>" placeholder="https://..."></div>
                        <div class="form-row">
                            <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" value="<?php echo $partner['sort_order'] ?? 0; ?>"></div>
                            <div class="form-group"><label>&nbsp;</label><label style="display:flex;align-items:center;gap:10px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" <?php echo ($partner['is_active'] ?? 1) ? 'checked' : ''; ?>><span>Aktif</span></label></div>
                        </div>
                        <div class="form-group"><label>Logo Partner</label><div class="file-upload" onclick="document.getElementById('logo').click()"><i class="fas fa-cloud-upload-alt"></i><p>Klik untuk upload logo</p><input type="file" id="logo" name="logo" accept="image/*"></div><div class="file-preview" id="logoPreview"><?php if ($partner && $partner['logo']): ?><img src="../uploads/partners/<?php echo $partner['logo']; ?>" alt="Preview"><?php endif; ?></div></div>
                        <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="partners.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() { document.getElementById('sidebar').classList.toggle('active'); });
        document.getElementById('logo')?.addEventListener('change', function(e) {
            const preview = document.getElementById('logoPreview'); const file = e.target.files[0];
            if (file) { const reader = new FileReader(); reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'; }; reader.readAsDataURL(file); }
        });
    </script>
</body>
</html>
