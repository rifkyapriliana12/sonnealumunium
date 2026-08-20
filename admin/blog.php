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
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $newFilename = 'blog-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/blog/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) $image = $newFilename;
        }
    }
    $slug = !empty($postData['slug']) ? strtolower(preg_replace('/[^a-z0-9-]/', '-', $postData['slug'])) : strtolower(preg_replace('/[^a-z0-9-]/', '-', $postData['title']));
    if ($postData['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, image, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([sanitize($postData['title']), $slug, sanitize($postData['excerpt']), $postData['content'], $image, isset($postData['is_active']) ? 1 : 0]);
        setFlash('Blog berhasil ditambahkan!'); header('Location: blog.php'); exit;
    } elseif ($postData['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, image=?, is_active=? WHERE id=?");
        $stmt->execute([sanitize($postData['title']), $slug, sanitize($postData['excerpt']), $postData['content'], $image, isset($postData['is_active']) ? 1 : 0, intval($postData['id'])]);
        setFlash('Blog berhasil diupdate!'); header('Location: blog.php'); exit;
    }
}
if ($action == 'delete' && $id) { $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?"); $stmt->execute([intval($id)]); setFlash('Blog berhasil dihapus!', 'error'); header('Location: blog.php'); exit; }
$post = null;
if ($action == 'edit' && $id) { $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?"); $stmt->execute([intval($id)]); $post = $stmt->fetch(); }
$posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Blog - Admin Sonne Aluminium</title>
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
            <a href="blog.php" class="nav-item active"><i class="fas fa-blog"></i><span>Blog</span></a>
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
                    <div class="table-header"><h2>Kelola Blog</h2><a href="blog.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Tulis Blog</a></div>
                    <?php if (empty($posts)): ?>
                        <div class="empty-state"><i class="fas fa-blog"></i><p>Belum ada artikel blog</p><a href="blog.php?action=add" class="btn btn-primary">Tulis Artikel Pertama</a></div>
                    <?php else: ?>
                        <table>
                            <thead><tr><th>Gambar</th><th>Judul</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php foreach ($posts as $p): ?>
                                    <tr>
                                        <td><?php if ($p['image']): ?><img src="../uploads/blog/<?php echo $p['image']; ?>" class="table-img" alt=""><?php else: ?><span>-</span><?php endif; ?></td>
                                        <td><?php echo $p['title']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                                        <td><span class="status-badge <?php echo $p['is_active'] ? 'status-active' : 'status-inactive'; ?>"><?php echo $p['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                        <td><div class="table-actions">
                                            <a href="blog.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="blog.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php elseif ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <h2><?php echo $action == 'add' ? 'Tulis Blog Baru' : 'Edit Blog'; ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($post): ?><input type="hidden" name="id" value="<?php echo $post['id']; ?>"><input type="hidden" name="existing_image" value="<?php echo $post['image']; ?>"><?php endif; ?>
                        <div class="form-group"><label>Judul *</label><input type="text" name="title" required value="<?php echo $post['title'] ?? ''; ?>"></div>
                        <div class="form-group"><label>Slug</label><input type="text" name="slug" value="<?php echo $post['slug'] ?? ''; ?>" placeholder="otomatis dari judul jika kosong"></div>
                        <div class="form-group"><label>Ringkasan</label><textarea name="excerpt" rows="3" placeholder="Ringkasan artikel"><?php echo $post['excerpt'] ?? ''; ?></textarea></div>
                        <div class="form-group"><label>Isi Konten</label><textarea name="content" rows="10" placeholder="Isi artikel lengkap"><?php echo $post['content'] ?? ''; ?></textarea></div>
                        <div class="form-group"><label>&nbsp;</label><label style="display:flex;align-items:center;gap:10px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" <?php echo ($post['is_active'] ?? 1) ? 'checked' : ''; ?>><span>Aktif</span></label></div>
                        <div class="form-group"><label>Gambar Featured</label><div class="file-upload" onclick="document.getElementById('image').click()"><i class="fas fa-cloud-upload-alt"></i><p>Klik untuk upload gambar</p><input type="file" id="image" name="image" accept="image/*"></div><div class="file-preview" id="imagePreview"><?php if ($post && $post['image']): ?><img src="../uploads/blog/<?php echo $post['image']; ?>" alt="Preview"><?php endif; ?></div></div>
                        <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="blog.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a></div>
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
