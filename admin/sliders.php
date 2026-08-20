<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();
$flash = getFlash();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postData = $_POST;
    
    // Handle file upload
    $image = $postData['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFilename = 'slider-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/sliders/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                $image = $newFilename;
            }
        }
    }

    if ($postData['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO sliders (title, subtitle, button_text, button_url, image, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            sanitize($postData['title']),
            sanitize($postData['subtitle']),
            sanitize($postData['button_text']),
            sanitize($postData['button_url']),
            $image,
            intval($postData['sort_order']),
            isset($postData['is_active']) ? 1 : 0
        ]);
        setFlash('Slider berhasil ditambahkan!');
        header('Location: sliders.php');
        exit;
    } elseif ($postData['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE sliders SET title=?, subtitle=?, button_text=?, button_url=?, image=?, sort_order=?, is_active=? WHERE id=?");
        $stmt->execute([
            sanitize($postData['title']),
            sanitize($postData['subtitle']),
            sanitize($postData['button_text']),
            sanitize($postData['button_url']),
            $image,
            intval($postData['sort_order']),
            isset($postData['is_active']) ? 1 : 0,
            intval($postData['id'])
        ]);
        setFlash('Slider berhasil diupdate!');
        header('Location: sliders.php');
        exit;
    }
}

// Handle delete
if ($action == 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->execute([intval($id)]);
    setFlash('Slider berhasil dihapus!', 'error');
    header('Location: sliders.php');
    exit;
}

// Get data for edit
$slider = null;
if ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM sliders WHERE id = ?");
    $stmt->execute([intval($id)]);
    $slider = $stmt->fetch();
}

// Get all sliders
$sliders = $pdo->query("SELECT * FROM sliders ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Slider - Admin Sonne Aluminium</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://sonnealuminium.com/wp-content/uploads/2025/07/LOGO-SONNE-2025-FINAL-GOLD-980x319.png" alt="Logo">
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="sliders.php" class="nav-item active">
                <i class="fas fa-images"></i>
                <span>Slider</span>
            </a>
            <a href="products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>
            <a href="projects.php" class="nav-item">
                <i class="fas fa-building"></i>
                <span>Proyek</span>
            </a>
            <a href="partners.php" class="nav-item">
                <i class="fas fa-handshake"></i>
                <span>Partner</span>
            </a>
            <a href="blog.php" class="nav-item">
                <i class="fas fa-blog"></i>
                <span>Blog</span>
            </a>
            <a href="testimonials.php" class="nav-item">
                <i class="fas fa-quote-right"></i>
                <span>Testimoni</span>
            </a>
            <a href="media.php" class="nav-item">
                <i class="fas fa-photo-video"></i>
                <span>Media</span>
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.html" target="_blank" class="nav-item">
                <i class="fas fa-external-link-alt"></i>
                <span>Lihat Website</span>
            </a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-right">
                <span class="admin-name">
                    <i class="fas fa-user-circle"></i>
                    <?php echo $_SESSION['admin_name']; ?>
                </span>
            </div>
        </header>

        <div class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <?php if ($action == 'list'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h2>Kelola Slider</h2>
                        <a href="sliders.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Slider
                        </a>
                    </div>

                    <?php if (empty($sliders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <p>Belum ada slider</p>
                            <a href="sliders.php?action=add" class="btn btn-primary">Tambah Slider Pertama</a>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sliders as $s): ?>
                                    <tr>
                                        <td>
                                            <?php if ($s['image']): ?>
                                                <img src="../uploads/sliders/<?php echo $s['image']; ?>" class="table-img" alt="">
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $s['title']; ?></td>
                                        <td><?php echo $s['sort_order']; ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $s['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $s['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="sliders.php?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="sliders.php?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

            <?php elseif ($action == 'add' || $action == 'edit'): ?>
                <div class="form-container">
                    <h2><?php echo $action == 'add' ? 'Tambah Slider' : 'Edit Slider'; ?></h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($slider): ?>
                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo $slider['image']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="title">Judul *</label>
                            <input type="text" id="title" name="title" required 
                                   value="<?php echo $slider['title'] ?? ''; ?>"
                                   placeholder="Masukkan judul slider">
                        </div>

                        <div class="form-group">
                            <label for="subtitle">Deskripsi</label>
                            <textarea id="subtitle" name="subtitle" rows="3"
                                      placeholder="Masukkan deskripsi slider"><?php echo $slider['subtitle'] ?? ''; ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="button_text">Teks Tombol</label>
                                <input type="text" id="button_text" name="button_text"
                                       value="<?php echo $slider['button_text'] ?? ''; ?>"
                                       placeholder="LIHAT PRODUCT">
                            </div>
                            <div class="form-group">
                                <label for="button_url">URL Tombol</label>
                                <input type="text" id="button_url" name="button_url"
                                       value="<?php echo $slider['button_url'] ?? ''; ?>"
                                       placeholder="/produk/">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sort_order">Urutan</label>
                                <input type="number" id="sort_order" name="sort_order"
                                       value="<?php echo $slider['sort_order'] ?? 0; ?>">
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" name="is_active" value="1"
                                           <?php echo ($slider['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    <span>Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image">Gambar Background</label>
                            <div class="file-upload" onclick="document.getElementById('image').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Klik untuk upload gambar</p>
                                <p style="font-size: 12px; color: #999;">Format: JPG, PNG, GIF, WebP (Max 50MB)</p>
                                <input type="file" id="image" name="image" accept="image/*">
                            </div>
                            <div class="file-preview" id="imagePreview">
                                <?php if ($slider && $slider['image']): ?>
                                    <img src="../uploads/sliders/<?php echo $slider['image']; ?>" alt="Preview">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="sliders.php" class="btn btn-danger">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Image preview
        document.getElementById('image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
