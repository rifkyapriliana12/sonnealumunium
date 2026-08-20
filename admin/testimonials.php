<?php
require_once 'config.php';
requireLogin();
$pdo = getDB();
$flash = getFlash();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = $_POST;
    $photo = $postData['existing_photo'] ?? '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $newFilename = 'testimonial-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/testimonials/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newFilename)) $photo = $newFilename;
        }
    }
    if ($postData['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO testimonials (client_name, company, message, photo, rating, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([sanitize($postData['client_name']), sanitize($postData['company']), sanitize($postData['message']), $photo, intval($postData['rating']), intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0]);
        setFlash('Testimoni berhasil ditambahkan!'); header('Location: testimonials.php'); exit;
    } elseif ($postData['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE testimonials SET client_name=?, company=?, message=?, photo=?, rating=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([sanitize($postData['client_name']), sanitize($postData['company']), sanitize($postData['message']), $photo, intval($postData['rating']), intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0, intval($postData['id'])]);
        setFlash('Testimoni berhasil diupdate!'); header('Location: testimonials.php'); exit;
    }
}
if ($action == 'delete' && $id) { $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?"); $stmt->execute([intval($id)]); setFlash('Testimoni berhasil dihapus!', 'error'); header('Location: testimonials.php'); exit; }
$testimonial = null;
if ($action == 'edit' && $id) { $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?"); $stmt->execute([intval($id)]); $testimonial = $stmt->fetch(); }
$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimoni - Admin Sonne Aluminium</title>
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
            <a href="partners.php" class="nav-item"><i class="fas fa-handshake"></i><span>Partner</span></a>
            <a href="blog.php" class="nav-item"><i class="fas fa-blog"></i><span>Blog</span></a>
            <a href="testimonials.php" class="nav-item active"><i class="fas fa-quote-right"></i><span>Testimoni</span></a>
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
                    <div class="table-header"><h2>Kelola Testimoni</h2><a href="testimonials.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Testimoni</a></div>
                    <?php if (empty($testimonials)): ?>
                        <div class="empty-state"><i class="fas fa-quote-right"></i><p>Belum ada testimoni</p><a href="testimonials.php?action=add" class="btn btn-primary">Tambah Testimoni Pertama</a></div>
                    <?php else: ?>
                        <table>
                            <thead><tr><th>Foto</th><th>Nama</th><th>Perusahaan</th><th>Pesan</th><th>Rating</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php foreach ($testimonials as $t): ?>
                                    <tr>
                                        <td><?php if ($t['photo']): ?><img src="../uploads/testimonials/<?php echo $t['photo']; ?>" class="table-img" alt=""><?php else: ?><span>-</span><?php endif; ?></td>
                                        <td><?php echo $t['client_name']; ?></td>
                                        <td><?php echo $t['company']; ?></td>
                                        <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo $t['message']; ?></td>
                                        <td><?php for($i = 1; $i <= 5; $i++): ?><i class="fas fa-star" style="color:<?php echo $i <= $t['rating'] ? '#d59203' : '#ddd'; ?>; font-size:12px;"></i><?php endfor; ?></td>
                                        <td><span class="status-badge <?php echo $t['is_active'] ? 'status-active' : 'status-inactive'; ?>"><?php echo $t['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                        <td><div class="table-actions">
                                            <a href="testimonials.php?action=edit&id=<?php echo $t['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="testimonials.php?action=delete&id=<?php echo $t['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php elseif ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <h2><?php echo $action == 'add' ? 'Tambah Testimoni' : 'Edit Testimoni'; ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($testimonial): ?><input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>"><input type="hidden" name="existing_photo" value="<?php echo $testimonial['photo']; ?>"><?php endif; ?>
                        <div class="form-row">
                            <div class="form-group"><label>Nama Klien *</label><input type="text" name="client_name" required value="<?php echo $testimonial['client_name'] ?? ''; ?>"></div>
                            <div class="form-group"><label>Perusahaan</label><input type="text" name="company" value="<?php echo $testimonial['company'] ?? ''; ?>"></div>
                        </div>
                        <div class="form-group"><label>Pesan *</label><textarea name="message" rows="4" required><?php echo $testimonial['message'] ?? ''; ?></textarea></div>
                        <div class="form-row">
                            <div class="form-group"><label>Rating</label><select name="rating"><?php for($i=5; $i>=1; $i--): ?><option value="<?php echo $i; ?>" <?php echo ($testimonial['rating'] ?? 5) == $i ? 'selected' : ''; ?>><?php echo $i; ?> Bintang</option><?php endfor; ?></select></div>
                            <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" value="<?php echo $testimonial['sort_order'] ?? 0; ?>"></div>
                            <div class="form-group"><label>&nbsp;</label><label style="display:flex;align-items:center;gap:10px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" <?php echo ($testimonial['is_active'] ?? 1) ? 'checked' : ''; ?>><span>Aktif</span></label></div>
                        </div>
                        <div class="form-group"><label>Foto Klien</label><div class="file-upload" onclick="document.getElementById('photo').click()"><i class="fas fa-cloud-upload-alt"></i><p>Klik untuk upload foto</p><input type="file" id="photo" name="photo" accept="image/*"></div><div class="file-preview" id="photoPreview"><?php if ($testimonial && $testimonial['photo']): ?><img src="../uploads/testimonials/<?php echo $testimonial['photo']; ?>" alt="Preview"><?php endif; ?></div></div>
                        <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="testimonials.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() { document.getElementById('sidebar').classList.toggle('active'); });
        document.getElementById('photo')?.addEventListener('change', function(e) {
            const preview = document.getElementById('photoPreview'); const file = e.target.files[0];
            if (file) { const reader = new FileReader(); reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'; }; reader.readAsDataURL(file); }
        });
    </script>
</body>
</html>
