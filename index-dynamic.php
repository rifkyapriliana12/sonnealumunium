<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Aluminium Doors, Aluminium Windows, Sliding Doors, Folding Doors, French Window, Swing Doors, Jakarta, Sonne |</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'admin/config.php'; $pdo = getDB(); $sliders = $pdo->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(); $products = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(); $projects = $pdo->query("SELECT * FROM projects WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(); $partners = $pdo->query("SELECT * FROM partners WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(); $testimonials = $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll(); $blogPosts = $pdo->query("SELECT * FROM blog_posts WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll(); $settings = []; $settingsQuery = $pdo->query("SELECT * FROM settings"); while ($row = $settingsQuery->fetch()) { $settings[$row['setting_key']] = $row['setting_value']; } ?>

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
                        <li><a href="#home" class="active">Home</a></li>
                        <li><a href="#products">Product</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#projects">Portfolio</a></li>
                        <li><a href="#why">Education</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#blog">Blog</a></li>
                        <li><a href="#partners">Showroom</a></li>
                    </ul>
                </nav>
                <div class="header-right">
                    <a href="#" class="btn-all-product">All Product</a>
                    <a href="#" class="btn-get-price-header">Get Price Estimates</a>
                </div>
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </header>

    <section class="hero-slider" id="home">
        <div class="slider-container">
            <?php foreach ($sliders as $index => $slider): ?>
                <div class="slide <?php echo $index == 0 ? 'active' : ''; ?>" style="background-image: linear-gradient(90deg,rgba(0,0,0,<?php echo $slider['overlay_opacity']; ?>) 5%,rgba(0,0,0,0.1) 35%), url('uploads/sliders/<?php echo $slider['image']; ?>')">
                    <div class="slide-content" <?php echo $index == 2 ? 'style="padding-right: 30%;"' : ''; ?>>
                        <?php if ($slider['subtitle']): ?><span class="slide-badge"><?php echo $slider['subtitle']; ?></span><?php endif; ?>
                        <h2 class="slide-title"><?php echo $slider['title']; ?></h2>
                        <?php if ($slider['button_text']): ?><a href="<?php echo $slider['button_url']; ?>" class="btn-slide"><?php echo $slider['button_text']; ?></a><?php endif; ?>
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

    <section class="heatproof-section">
        <div class="container">
            <h2>HEATPROOF AND SOUNDPROOF : DOORS AND WINDOWS</h2>
            <p>We offer German-inspired <strong>thermal-break technology</strong> and <strong>hollow double-glass</strong> for superior heat &amp; sound insulation at reasonable values.</p>
        </div>
    </section>

    <section class="products-showcase" id="products">
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

    <section class="why-section" id="why">
        <div class="container">
            <h2 class="section-title center">WHY CHOSE SONNE?</h2>
            <div class="title-line center"></div>
            <div class="why-intro">
                <p>We deliver what you ask.</p>
            </div>
            <div class="why-problems">
                <div class="problem"><i class="fas fa-times-circle"></i><span>High prices do not guarantee quality and beauty.</span></div>
                <div class="problem"><i class="fas fa-times-circle"></i><span>Products delivered are of less quality than those shown in the showroom.</span></div>
                <div class="problem"><i class="fas fa-times-circle"></i><span>Lack of transparency and product education</span></div>
            </div>
            <div class="why-subtitle">
                <h3>Founded to solve real, personal problems.</h3>
                <p>SONNE was created to solve our founders' problems during home renovation and following their 32 years of experience in aluminium:</p>
            </div>
            <div class="why-features">
                <div class="feature-card">
                    <div class="feature-icon"><img src="https://sonnealuminium.com/wp-content/uploads/2023/09/certificate.png" alt="Certificate"></div>
                    <h3>Global company with ISO9001 certified supply chain</h3>
                    <ul>
                        <li>Headquartered in Singapore, SONNE has an ISO9001 factory partner with &gt;70,000 sqm size.</li>
                        <li>The products scored very high in all performance tests (water, air, thermal, wind, sound)</li>
                        <li>Products are certified to enter the US, EU, AU, China, SA market including safety certificate.</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="https://sonnealuminium.com/wp-content/uploads/2023/09/idea.png" alt="Idea"></div>
                    <h3>Luxurious and innovative design</h3>
                    <ul>
                        <li>No customer ask is too troublesome for us:</li>
                        <li>SONNE design has many options: sleek for modern design, strong for American classic, minimalist for Scandinavian taste</li>
                        <li>SONNE personalizes everything from drawings, glass, colour (10+ choices), accessories, etc</li>
                    </ul>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="https://sonnealuminium.com/wp-content/uploads/2023/08/think.png" alt="Think"></div>
                    <h3>Durability for decades to come</h3>
                    <ul>
                        <li>Our aluminium is 6063 T5 aviation grade with top colour-coating &amp; thermal-break technology.</li>
                        <li>Our glass &amp; sealant is automotive grade; double glass for best heat &amp; sound-proof</li>
                        <li>Our accessories passed thousands of opening/closing tests</li>
                        <li>We commit to our after-sales warranty</li>
                    </ul>
                </div>
            </div>
            <div class="why-buttons center">
                <a href="#" class="btn-primary">LEARN MORE ABOUT SONNE</a>
                <a href="#" class="btn-outline-dark">GET PRICE ESTIMATES</a>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title center">WHAT OUR CUSTOMERS SAY</h2>
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

    <section class="projects-section" id="projects">
        <div class="container">
            <h2 class="section-title center">GLOBAL PROJECT CASES</h2>
            <div class="title-line center"></div>
            <div class="projects-carousel">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <img src="uploads/projects/<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>">
                        <div class="project-overlay"><h3><?php echo $project['title']; ?></h3></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="center"><a href="#" class="btn-outline-dark">EXPLORE ALL PROJECTS</a></div>
        </div>
    </section>

    <section class="partners-section" id="partners">
        <div class="container">
            <h2 class="section-title center">MEET OUR GLOBAL PARTNERS</h2>
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

    <section class="blog-section" id="blog">
        <div class="container">
            <h2 class="section-title center">OUR LATEST BLOG</h2>
            <div class="title-line center"></div>
            <div class="blog-grid">
                <?php foreach ($blogPosts as $post): ?>
                    <div class="blog-card">
                        <div class="blog-image"><img src="uploads/blog/<?php echo $post['image']; ?>" alt="<?php echo $post['title']; ?>"></div>
                        <div class="blog-content">
                            <h3><?php echo $post['title']; ?></h3>
                            <p><?php echo $post['excerpt']; ?></p>
                            <a href="blog-detail.php?slug=<?php echo $post['slug']; ?>" class="btn-outline">READ MORE</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta-section" id="contact">
        <div class="container">
            <h2>CONTACT US AND SHARE YOUR DRAWING</h2>
            <div class="cta-buttons">
                <a href="#" class="btn-primary">GET PRICE ESTIMATES</a>
                <a href="#" class="btn-primary">BE OUR BUSINESS PARTNER</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-top">
                <img src="https://sonnealuminium.com/wp-content/uploads/2025/07/LOGO-SONNE-2025-FINAL-GOLD-980x319.png" alt="Sonne Aluminium" class="footer-logo">
                <a href="mailto:sonnealuminium.adm@gmail.com" class="footer-email">sonnealuminium.adm@gmail.com</a>
            </div>
            <div class="footer-social">
                <?php if (!empty($settings['instagram_url'])): ?><a href="<?php echo $settings['instagram_url']; ?>" target="_blank" class="social-link instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
                <?php if (!empty($settings['facebook_url'])): ?><a href="<?php echo $settings['facebook_url']; ?>" target="_blank" class="social-link facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
                <?php if (!empty($settings['tiktok_url'])): ?><a href="<?php echo $settings['tiktok_url']; ?>" target="_blank" class="social-link tiktok"><i class="fab fa-tiktok"></i></a><?php endif; ?>
                <?php if (!empty($settings['youtube_url'])): ?><a href="<?php echo $settings['youtube_url']; ?>" target="_blank" class="social-link youtube"><i class="fab fa-youtube"></i></a><?php endif; ?>
                <?php if (!empty($settings['linkedin_url'])): ?><a href="<?php echo $settings['linkedin_url']; ?>" target="_blank" class="social-link linkedin"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
            </div>
            <div class="footer-columns">
                <div class="footer-col">
                    <h4>Product</h4>
                    <ul><li><a href="#">Door</a></li><li><a href="#">Window</a></li><li><a href="#">Bathroom Cubicle, Canopy &amp; Railing</a></li></ul>
                </div>
                <div class="footer-col">
                    <h4>About</h4>
                    <ul><li><a href="#">Company Background</a></li><li><a href="#">Product Differentiation</a></li><li><a href="#">Customization</a></li></ul>
                </div>
                <div class="footer-col">
                    <h4>Global Headquarter</h4>
                    <p>Punggol Field<br>Punggol, Singapore 828817</p>
                </div>
                <div class="footer-col">
                    <h4>Indonesia Branch &amp; Showroom</h4>
                    <p><?php echo $settings['site_address'] ?? 'Jalan Raya Narogong Km7, Bekasi, Indonesia 17116'; ?></p>
                    <a href="#" class="btn-showroom">SHOWROOM LOCATION</a>
                </div>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2023-<?php echo date('Y'); ?> Sonne Aluminium. All rights reserved. Developed by <a href="https://nectar.id" target="_blank">Nectar Website</a>.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/<?php echo $settings['site_whatsapp'] ?? '6281282006363'; ?>" class="whatsapp-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
    <a href="#" class="back-to-top" id="backToTop"><i class="fas fa-arrow-up"></i></a>

    <script src="script.js"></script>
</body>
</html>
