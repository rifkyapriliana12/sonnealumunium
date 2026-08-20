<?php
require_once 'config.php';
requireLogin();
$pdo = getDB();
$flash = getFlash();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = $_POST;
    $image = $postData['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newFilename = 'project-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/projects/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) $image = $newFilename;
        }
    }
    if ($postData['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([sanitize($postData['title']), sanitize($postData['description']), $image, sanitize($postData['url']), intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0]);
        setFlash('Proyek berhasil ditambahkan!');
        header('Location: projects.php'); exit;
    } elseif ($postData['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, image=?, url=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([sanitize($postData['title']), sanitize($postData['description']), $image, sanitize($postData['url']), intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0, intval($postData['id'])]);
        setFlash('Proyek berhasil diupdate!');
        header('Location: projects.php'); exit;
    }
}
if ($action == 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([intval($id)]);
    setFlash('Proyek berhasil dihapus!', 'error');
    header('Location: projects.php'); exit;
}
$project = null;
if ($action == 'edit' && $id) { $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?"); $stmt->execute([intval($id)]); $project = $stmt->fetch(); }
$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Proyek - Admin Sonne Aluminium</title>
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
            <a href="projects.php" class="nav-item active"><i class="fas fa-building"></i><span>Proyek</span></a>
            <a href="partners.php" class="nav-item"><i class="fas fa-handshake"></i><span>Partner</span></a>
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
                    <div class="table-header"><h2>Kelola Proyek</h2><a href="projects.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Proyek</a></div>
                    <?php if (empty($projects)): ?>
                        <div class="empty-state"><i class="fas fa-building"></i><p>Belum ada proyek</p><a href="projects.php?action=add" class="btn btn-primary">Tambah Proyek Pertama</a></div>
                    <?php else: ?>
                        <table>
                            <thead><tr><th>Gambar</th><th>Judul</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php foreach ($projects as $p): ?>
                                    <tr>
                                        <td><?php if ($p['image']): ?><img src="../uploads/projects/<?php echo $p['image']; ?>" class="table-img" alt=""><?php else: ?><span>-</span><?php endif; ?></td>
                                        <td><?php echo $p['title']; ?></td>
                                        <td><?php echo $p['sort_order']; ?></td>
                                        <td><span class="status-badge <?php echo $p['is_active'] ? 'status-active' : 'status-inactive'; ?>"><?php echo $p['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                        <td><div class="table-actions">
                                            <a href="projects.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="projects.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php elseif ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <h2><?php echo $action == 'add' ? 'Tambah Proyek' : 'Edit Proyek'; ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($project): ?><input type="hidden" name="id" value="<?php echo $project['id']; ?>"><input type="hidden" name="existing_image" value="<?php echo $project['image']; ?>"><?php endif; ?>
                        <div class="form-group"><label>Judul *</label><input type="text" name="title" required value="<?php echo $project['title'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="3"><?php echo $project['description'] ?? ''; ?></textarea></div>
                        <div class="form-group"><label>URL</label><input type="text" name="url" value="<?php echo $project['url'] ?? ''; ?>" placeholder="/project/american-classic/"></div>
                        <div class="form-row">
                            <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" value="<?php echo $project['sort_order'] ?? 0; ?>"></div>
                            <div class="form-group"><label>&nbsp;</label><label style="display:flex;align-items:center;gap:10px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" <?php echo ($project['is_active'] ?? 1) ? 'checked' : ''; ?>><span>Aktif</span></label></div>
                        </div>
                        <div class="form-group"><label>Gambar Proyek</label><div class="file-upload" onclick="document.getElementById('image').click()"><i class="fas fa-cloud-upload-alt"></i><p>Klik untuk upload gambar</p><input type="file" id="image" name="image" accept="image/*"></div><div class="file-preview" id="imagePreview"><?php if ($project && $project['image']): ?><img src="../uploads/projects/<?php echo $project['image']; ?>" alt="Preview"><?php endif; ?></div></div>
                        <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="projects.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() { document.getElementById('sidebar').classList.toggle('active'); });
        document.getElementById('image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview'); const file = e.target.files[0];
            if (file) { const reader = new FileReader(); reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'; }; reader.readAsDataURL(file); }
        });
    </script>
</body>
</html>
