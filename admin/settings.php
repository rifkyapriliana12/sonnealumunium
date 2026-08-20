<?php
require_once 'config.php';
requireLogin();
$pdo = getDB();
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settings = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_description' => sanitize($_POST['site_description'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'whatsapp' => sanitize($_POST['whatsapp'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'address' => sanitize($_POST['address'] ?? ''),
        'instagram' => sanitize($_POST['instagram'] ?? ''),
        'facebook' => sanitize($_POST['facebook'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
        'youtube' => sanitize($_POST['youtube'] ?? ''),
        'tiktok' => sanitize($_POST['tiktok'] ?? ''),
        'google_maps' => $_POST['google_maps'] ?? '',
        'footer_text' => sanitize($_POST['footer_text'] ?? ''),
        'cta_title' => sanitize($_POST['cta_title'] ?? ''),
        'cta_description' => sanitize($_POST['cta_description'] ?? ''),
    ];
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text') ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    setFlash('Pengaturan berhasil disimpan!'); header('Location: settings.php'); exit;
}
$settings = [];
$rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
foreach ($rows as $row) $settings[$row['setting_key']] = $row['setting_value'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Sonne Aluminium</title>
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
            <a href="testimonials.php" class="nav-item"><i class="fas fa-quote-right"></i><span>Testimoni</span></a>
            <a href="media.php" class="nav-item"><i class="fas fa-photo-video"></i><span>Media</span></a>
            <a href="settings.php" class="nav-item active"><i class="fas fa-cog"></i><span>Pengaturan</span></a>
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
            <div class="form-container">
                <h2><i class="fas fa-cog"></i> Pengaturan Website</h2>
                <form method="POST">
                    <h3 style="color:#d59203;margin:20px 0 10px;border-bottom:1px solid #eee;padding-bottom:8px;"><i class="fas fa-info-circle"></i> Informasi Umum</h3>
                    <div class="form-group"><label>Nama Website</label><input type="text" name="site_name" value="<?php echo $settings['site_name'] ?? 'Sonne Aluminium'; ?>"></div>
                    <div class="form-group"><label>Deskripsi Website</label><textarea name="site_description" rows="2"><?php echo $settings['site_description'] ?? 'Your Trusted Aluminium Partner'; ?></textarea></div>

                    <h3 style="color:#d59203;margin:20px 0 10px;border-bottom:1px solid #eee;padding-bottom:8px;"><i class="fas fa-phone"></i> Kontak</h3>
                    <div class="form-row">
                        <div class="form-group"><label>Nomor Telepon</label><input type="text" name="phone" value="<?php echo $settings['phone'] ?? '021-29455306'; ?>" placeholder="021-XXXXXXXX"></div>
                        <div class="form-group"><label>Nomor WhatsApp</label><input type="text" name="whatsapp" value="<?php echo $settings['whatsapp'] ?? '6281282006363'; ?>" placeholder="628XXXXXXXXXX"></div>
                    </div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo $settings['email'] ?? 'sonnealuminium.adm@gmail.com'; ?>"></div>
                    <div class="form-group"><label>Alamat</label><textarea name="address" rows="2"><?php echo $settings['address'] ?? 'Rukan Green Lake Sunter, Jl. Danau Sunter Utara Raya Blok A13 No. 01, Jakarta 14350, Indonesia'; ?></textarea></div>

                    <h3 style="color:#d59203;margin:20px 0 10px;border-bottom:1px solid #eee;padding-bottom:8px;"><i class="fas fa-share-alt"></i> Media Sosial</h3>
                    <div class="form-row">
                        <div class="form-group"><label><i class="fab fa-instagram"></i> Instagram</label><input type="text" name="instagram" value="<?php echo $settings['instagram'] ?? ''; ?>" placeholder="https://instagram.com/..."></div>
                        <div class="form-group"><label><i class="fab fa-facebook"></i> Facebook</label><input type="text" name="facebook" value="<?php echo $settings['facebook'] ?? ''; ?>" placeholder="https://facebook.com/..."></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label><i class="fab fa-linkedin"></i> LinkedIn</label><input type="text" name="linkedin" value="<?php echo $settings['linkedin'] ?? ''; ?>" placeholder="https://linkedin.com/..."></div>
                        <div class="form-group"><label><i class="fab fa-youtube"></i> YouTube</label><input type="text" name="youtube" value="<?php echo $settings['youtube'] ?? ''; ?>" placeholder="https://youtube.com/..."></div>
                    </div>
                    <div class="form-group"><label><i class="fab fa-tiktok"></i> TikTok</label><input type="text" name="tiktok" value="<?php echo $settings['tiktok'] ?? ''; ?>" placeholder="https://tiktok.com/@..."></div>

                    <h3 style="color:#d59203;margin:20px 0 10px;border-bottom:1px solid #eee;padding-bottom:8px;"><i class="fas fa-map-marker-alt"></i> Google Maps</h3>
                    <div class="form-group"><label>Embed Code Google Maps</label><textarea name="google_maps" rows="4" placeholder="Paste embed code iframe dari Google Maps"><?php echo $settings['google_maps'] ?? ''; ?></textarea></div>

                    <h3 style="color:#d59203;margin:20px 0 10px;border-bottom:1px solid #eee;padding-bottom:8px;"><i class="fas fa-bullhorn"></i> Call to Action</h3>
                    <div class="form-group"><label>CTA Judul</label><input type="text" name="cta_title" value="<?php echo $settings['cta_title'] ?? 'Ready to Transform Your Space?'; ?>"></div>
                    <div class="form-group"><label>CTA Deskripsi</label><textarea name="cta_description" rows="2"><?php echo $settings['cta_description'] ?? "Let's create something extraordinary together"; ?></textarea></div>

                    <h3 style="color:#d59203;margin:20px 0 10px;border-bottom:1px solid #eee;padding-bottom:8px;"><i class="fas fa-align-left"></i> Footer</h3>
                    <div class="form-group"><label>Teks Footer</label><textarea name="footer_text" rows="2"><?php echo $settings['footer_text'] ?? 'Sonne Aluminium is a registered company under the laws of the Republic of Indonesia.'; ?></textarea></div>

                    <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua Pengaturan</button></div>
                </form>
            </div>
        </div>
    </main>
    <script>document.getElementById('menuToggle').addEventListener('click', function() { document.getElementById('sidebar').classList.toggle('active'); });</script>
</body>
</html>
