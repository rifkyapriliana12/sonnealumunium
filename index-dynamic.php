<?php
// Frontend PHP - Fetches data from database
require_once 'admin/config.php';

$pdo = getDB();

// Get sliders
$sliders = $pdo->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Get products
$products = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Get projects
$projects = $pdo->query("SELECT * FROM projects WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Get partners
$partners = $pdo->query("SELECT * FROM partners WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Get testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Get blog posts
$blogPosts = $pdo->query("SELECT * FROM blog_posts WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Get settings
$settings = [];
$settingsQuery = $pdo->query("SELECT * FROM settings");
while ($row = $settingsQuery->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="id-ID">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Aluminium Door, Aluminium Window, Sliding Door, Folding Door, French Window, Swing Door, Pintu Aluminium, Jendela Aluminium, Kusen Aluminium, Jakarta, Sonne, Sonne Aluminium |</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sticky Header -->
    <header class="header" id="mainHeader">
        <div class="container">
            <div class="header-inner">
                <div class="logo">
                    <a href="#">
                        <img src="https://sonnealuminium.com/wp-content/uploads/2025/07/LOGO-SONNE-2025-FINAL-GOLD-980x319.png" alt="Sonne Aluminium">
                    </a>
                </div>
                <nav class="main-nav" id="mainNav">
                    <ul>
                        <li><a href="#" class="active">Home</a></li>
                        <li><a href="#">Produk</a></li>
                        <li><a href="#">Kontak</a></li>
                        <li><a href="#">Portofolio</a></li>
                        <li><a href="#">Education</a></li>
                        <li><a href="#">Tentang</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Showroom</a></li>
                    </ul>
                </nav>
                <div class="header-right">
                    <a href="#" class="btn-all-product">All Product</a>
                    <a href="#" class="btn-get-price-header">Get Price Estimates</a>
                </div>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Slider -->
    <section class="hero-slider">
        <div class="slider-container">
            <?php foreach ($sliders as $index => $slider): ?>
                <div class="slide <?php echo $index == 0 ? 'active' : ''; ?>" 
                     style="background-image: linear-gradient(90deg,rgba(0,0,0,<?php echo $slider['overlay_opacity']; ?>) 5%,rgba(0,0,0,0.1) 35%), url('uploads/sliders/<?php echo $slider['image']; ?>')">
                    <div class="slide-content" <?php echo $index == 2 ? 'style="padding-right: 30%;"' : ''; ?>>
                        <?php if ($slider['subtitle']): ?>
                            <span class="slide-badge"><?php echo $slider['subtitle']; ?></span>
                        <?php endif; ?>
                        <h2 class="slide-title"><?php echo $slider['title']; ?></h2>
                        <?php if ($slider['button_text']): ?>
                            <a href="<?php echo $slider['button_url']; ?>" class="btn-slide"><?php echo $slider['button_text']; ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="slider-controls">
            <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="slider-dots">
            <?php foreach ($sliders as $index => $slider): ?>
                <span class="dot <?php echo $index == 0 ? 'active' : ''; ?>"></span>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Product Tabs Section -->
    <section class="product-tabs-section">
        <div class="container">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="all">All Product</button>
                <button class="tab-btn" data-tab="door">Door</button>
                <button class="tab-btn" data-tab="window">Window</button>
                <button class="tab-btn" data-tab="garages">Garages</button>
                <button class="tab-btn" data-tab="shower">Shower</button>
                <button class="tab-btn" data-tab="canopy">Canopy & Railing</button>
            </div>
        </div>
    </section>

    <!-- Heatproof Section -->
    <section class="heatproof-section">
        <div class="container">
            <h2>HEATPROOF AND SOUNDPROOF : DOORS AND WINDOW</h2>
            <p>Kami menawarkan teknologi thermal-break yang terinspirasi dari Jerman dan double glass berongga untuk isolasi panas dan suara yang lebih baik dengan nilai yang terjangkau</p>
        </div>
    </section>

    <!-- Products Showcase -->
    <section class="products-showcase">
        <div class="container">
            <?php foreach ($products as $index => $product): ?>
                <div class="product-row <?php echo $index % 2 == 1 ? 'reverse' : ''; ?>">
                    <div class="product-text">
                        <span class="product-label"><?php echo $product['label']; ?></span>
                        <h3><?php echo $product['title']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <a href="<?php echo $product['button_url']; ?>" class="btn-outline"><?php echo $product['button_text']; ?></a>
                    </div>
                    <div class="product-image">
                        <img src="uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Why Choose SONNE -->
    <section class="why-section">
        <div class="container">
            <h2 class="section-title center">Kenapa memilih SONNE?</h2>
            <div class="title-line center"></div>

            <div class="why-intro">
                <p>Kami menghasilkan produk yang benar-benar Anda minta.</p>
                <p class="subtitle">Didirikan untuk menyelesaikan masalah yang nyata dan personal</p>
                <p>SONNE diciptakan oleh founder kami saat mengalami kesulitan dalam proses renovasi rumah dan dengan pengetahuan mereka selama 32 tahun dalam dunia aluminium, masalah tersebut adalah:</p>
            </div>

            <div class="why-problems">
                <div class="problem">
                    <i class="fas fa-times-circle"></i>
                    <span>Harga yang tinggi tidak menjamin kualitas dan estetika</span>
                </div>
                <div class="problem">
                    <i class="fas fa-times-circle"></i>
                    <span>Produk yang dihasilkan dibawah yang ditunjukan di showroom</span>
                </div>
                <div class="problem">
                    <i class="fas fa-times-circle"></i>
                    <span>Kurangnya transparansi dan edukasi produk</span>
                </div>
            </div>

            <div class="why-features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="https://sonnealuminium.com/wp-content/uploads/2023/09/certificate.png" alt="Certificate">
                    </div>
                    <h3>Perusahaan global dengan sertifikasi ISO9001 untuk jalur rantai pasoknya</h3>
                    <p>Dengan kantor pusat di Singapura, SONNE memiliki Kerjasama dengan pabrik bersertifikat ISO9001 dengan ukuran >70,000 sqm.</p>
                    <a href="#" class="btn-outline">LIHAT SELENGKAPNYA</a>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="https://sonnealuminium.com/wp-content/uploads/2023/09/idea.png" alt="Idea">
                    </div>
                    <h3>Desain yang mewah dan inovatif</h3>
                    <p>Tidak ada permintaan customer yang terlalu menyulitkan untuk kami:</p>
                    <a href="#" class="btn-outline">LIHAT SELENGKAPNYA</a>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="https://sonnealuminium.com/wp-content/uploads/2023/08/think.png" alt="Think">
                    </div>
                    <h3>Keawetan untuk dekade yang akan datang</h3>
                    <p>Aluminium kami adalah tipe penerbangan 6063 T5 dengan pelapisan warna terbaik & teknologi thermal-break.</p>
                    <a href="#" class="btn-outline">LIHAT SELENGKAPNYA</a>
                </div>
            </div>

            <div class="why-buttons center">
                <a href="#" class="btn-primary">Tentang Sonne</a>
                <a href="#" class="btn-outline-dark">CEK PENAWARAN HARGA</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title center">APA KATA PELANGGAN KAMI</h2>
            <div class="title-line center"></div>

            <div class="testimonials-slider">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="testimonial <?php echo $index == 0 ? 'active' : ''; ?>">
                        <p>"<?php echo $testimonial['message']; ?>"</p>
                        <div class="testimonial-author">
                            <strong><?php echo $testimonial['client_name']; ?></strong>
                            <span><?php echo $testimonial['company']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="testimonial-dots">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <span class="dot <?php echo $index == 0 ? 'active' : ''; ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Projects -->
    <section class="projects-section">
        <div class="container">
            <h2 class="section-title center">PROYEK GLOBAL KAMI</h2>
            <div class="title-line center"></div>

            <div class="projects-carousel">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <img src="uploads/projects/<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>">
                        <div class="project-overlay">
                            <h3><?php echo $project['title']; ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="center">
                <a href="#" class="btn-outline-dark">LIHAT PROYEK</a>
            </div>
        </div>
    </section>

    <!-- Partners -->
    <section class="partners-section">
        <div class="container">
            <h2 class="section-title center">PARTNER GLOBAL KAMI</h2>
            <div class="title-line center"></div>

            <div class="partners-grid">
                <?php foreach ($partners as $partner): ?>
                    <div class="partner-item">
                        <a href="<?php echo $partner['url']; ?>" target="_blank">
                            <img src="uploads/partners/<?php echo $partner['logo']; ?>" alt="<?php echo $partner['name']; ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Blog -->
    <section class="blog-section">
        <div class="container">
            <h2 class="section-title center">OUR LATEST BLOG</h2>
            <div class="title-line center"></div>

            <div class="blog-grid">
                <?php foreach ($blogPosts as $post): ?>
                    <div class="blog-card">
                        <div class="blog-image">
                            <img src="uploads/blog/<?php echo $post['image']; ?>" alt="<?php echo $post['title']; ?>">
                        </div>
                        <div class="blog-content">
                            <h3><?php echo $post['title']; ?></h3>
                            <p><?php echo $post['excerpt']; ?></p>
                            <a href="blog-detail.php?slug=<?php echo $post['slug']; ?>" class="btn-outline">Baca Selengkapnya</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>HUBUNGI KAMI DAN BAGIKAN GAMBAR RUMAHMU</h2>
            <div class="cta-buttons">
                <a href="#" class="btn-primary">CEK PENAWARAN HARGA</a>
                <a href="#" class="btn-primary">JADI BISNIS PARTNER KAMI</a>
                <a href="#" class="btn-primary">LOKASI SHOWROOM</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-social">
                <?php if (!empty($settings['instagram_url'])): ?>
                    <a href="<?php echo $settings['instagram_url']; ?>" target="_blank" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['facebook_url'])): ?>
                    <a href="<?php echo $settings['facebook_url']; ?>" target="_blank" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['tiktok_url'])): ?>
                    <a href="<?php echo $settings['tiktok_url']; ?>" target="_blank" class="social-link tiktok"><i class="fab fa-tiktok"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['youtube_url'])): ?>
                    <a href="<?php echo $settings['youtube_url']; ?>" target="_blank" class="social-link youtube"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['linkedin_url'])): ?>
                    <a href="<?php echo $settings['linkedin_url']; ?>" target="_blank" class="social-link linkedin"><i class="fab fa-linkedin-in"></i></a>
                <?php endif; ?>
            </div>

            <div class="footer-columns">
                <div class="footer-col">
                    <h4>Produk</h4>
                    <ul>
                        <li><a href="#">Pintu</a></li>
                        <li><a href="#">Jendela</a></li>
                        <li><a href="#">Bilik Kamar Mandi, Kanopi & Railing</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Tentang</h4>
                    <ul>
                        <li><a href="#">Background Perusahaan</a></li>
                        <li><a href="#">Diferensiasi Produk</a></li>
                        <li><a href="#">Customization</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Kantor Pusat</h4>
                    <p>Punggol Field<br>Punggol, Singapore 828817</p>
                </div>
                <div class="footer-col">
                    <h4>Showroom & Cabang Indonesia</h4>
                    <p><?php echo $settings['site_address'] ?? 'Jalan Raya Narogong Km7, Bekasi, Indonesia 17116'; ?></p>
                    <a href="#" class="btn-showroom">LOKASI SHOWROOM</a>
                </div>
            </div>

            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> Sonne Aluminium. All rights reserved. Developed by <a href="https://nectar.id" target="_blank">Nectar Website</a>.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/<?php echo $settings['site_whatsapp'] ?? '6281282006363'; ?>" class="whatsapp-btn" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Back to Top -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script src="script.js"></script>
</body>
</html>
