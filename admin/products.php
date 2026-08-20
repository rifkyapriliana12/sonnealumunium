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
            $newFilename = 'product-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                $image = $newFilename;
            }
        }
    }

    if ($postData['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO products (category, label, title, description, button_text, button_url, image, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([sanitize($postData['category']), sanitize($postData['label']), sanitize($postData['title']), sanitize($postData['description']), sanitize($postData['button_text']), sanitize($postData['button_url']), $image, intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0]);
        setFlash('Produk berhasil ditambahkan!');
        header('Location: products.php');
        exit;
    } elseif ($postData['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE products SET category=?, label=?, title=?, description=?, button_text=?, button_url=?, image=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([sanitize($postData['category']), sanitize($postData['label']), sanitize($postData['title']), sanitize($postData['description']), sanitize($postData['button_text']), sanitize($postData['button_url']), $image, intval($postData['sort_order']), isset($postData['is_active']) ? 1 : 0, intval($postData['id'])]);
        setFlash('Produk berhasil diupdate!');
        header('Location: products.php');
        exit;
    }
}

if ($action == 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([intval($id)]);
    setFlash('Produk berhasil dihapus!', 'error');
    header('Location: products.php');
    exit;
}

$product = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([intval($id)]);
    $product = $stmt->fetch();
}

$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Sonne Aluminium</title>
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
            <a href="products.php" class="nav-item active"><i class="fas fa-box"></i><span>Produk</span></a>
            <a href="projects.php" class="nav-item"><i class="fas fa-building"></i><span>Proyek</span></a>
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
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="topbar-right"><span class="admin-name"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['admin_name']; ?></span></div>
        </header>
        <div class="content">
            <?php if ($flash): ?><div class="alert alert-<?php echo $flash['type']; ?>"><?php echo $flash['message']; ?></div><?php endif; ?>

            <?php if ($action == 'list'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h2>Kelola Produk</h2>
                        <a href="products.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk</a>
                    </div>
                    <?php if (empty($products)): ?>
                        <div class="empty-state"><i class="fas fa-box"></i><p>Belum ada produk</p><a href="products.php?action=add" class="btn btn-primary">Tambah Produk Pertama</a></div>
                    <?php else: ?>
                        <table>
                            <thead><tr><th>Gambar</th><th>Kategori</th><th>Judul</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><?php if ($p['image']): ?><img src="../uploads/products/<?php echo $p['image']; ?>" class="table-img" alt=""><?php else: ?><span>-</span><?php endif; ?></td>
                                        <td><?php echo ucfirst($p['category']); ?></td>
                                        <td><?php echo $p['title']; ?></td>
                                        <td><?php echo $p['sort_order']; ?></td>
                                        <td><span class="status-badge <?php echo $p['is_active'] ? 'status-active' : 'status-inactive'; ?>"><?php echo $p['is_active'] ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                        <td><div class="table-actions">
                                            <a href="products.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="products.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i></a>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php elseif ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <h2><?php echo $action == 'add' ? 'Tambah Produk' : 'Edit Produk'; ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($product): ?><input type="hidden" name="id" value="<?php echo $product['id']; ?>"><input type="hidden" name="existing_image" value="<?php echo $product['image']; ?>"><?php endif; ?>
                        <div class="form-row">
                            <div class="form-group"><label>Kategori *</label><select name="category" required><option value="door">Door</option><option value="window">Window</option><option value="garages">Garages</option><option value="shower">Shower</option><option value="canopy">Canopy & Railing</option></select></div>
                            <div class="form-group"><label>Label</label><input type="text" name="label" value="<?php echo $product['label'] ?? ''; ?>" placeholder="EXQUISITES"></div>
                        </div>
                        <div class="form-group"><label>Judul *</label><input type="text" name="title" required value="<?php echo $product['title'] ?? ''; ?>" placeholder="Nama produk"></div>
                        <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="4" placeholder="Deskripsi produk"><?php echo $product['description'] ?? ''; ?></textarea></div>
                        <div class="form-row">
                            <div class="form-group"><label>Teks Tombol</label><input type="text" name="button_text" value="<?php echo $product['button_text'] ?? ''; ?>" placeholder="EXPLORE THE COLLECTION"></div>
                            <div class="form-group"><label>URL Tombol</label><input type="text" name="button_url" value="<?php echo $product['button_url'] ?? ''; ?>" placeholder="/produk/pintu/"></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" value="<?php echo $product['sort_order'] ?? 0; ?>"></div>
                            <div class="form-group"><label>&nbsp;</label><label style="display:flex;align-items:center;gap:10px;cursor:pointer;"><input type="checkbox" name="is_active" value="1" <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>><span>Aktif</span></label></div>
                        </div>
                        <div class="form-group"><label>Gambar Produk</label><div class="file-upload" onclick="document.getElementById('image').click()"><i class="fas fa-cloud-upload-alt"></i><p>Klik untuk upload gambar</p><input type="file" id="image" name="image" accept="image/*"></div><div class="file-preview" id="imagePreview"><?php if ($product && $product['image']): ?><img src="../uploads/products/<?php echo $product['image']; ?>" alt="Preview"><?php endif; ?></div></div>
                        <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="products.php" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() { document.getElementById('sidebar').classList.toggle('active'); });
        document.getElementById('image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            if (file) { const reader = new FileReader(); reader.onload = function(e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">'; }; reader.readAsDataURL(file); }
        });
    </script>
</body>
</html>
