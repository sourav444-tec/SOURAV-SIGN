<?php
declare(strict_types=1);

/**
 * Homepage.
 * Uses the shared header/footer. Product and category data comes from
 * includes/sample-data.php (TEMPORARY sample data, replaced in a later phase).
 */

// Page settings read by includes/header.php
$pageTitle       = '';   // empty = default "Sourav Sign Stamp Store – Your Sign • Our Quality"
$metaDescription = 'Custom stamps, rubber stamps, company seals, name plates and sign boards from Sourav Sign Stamp Store.';
$navActive       = 'home';
$bodyClass       = 'page-home';

require_once __DIR__ . '/includes/header.php';       // loads config, starts session, prints <head> + navbar
require_once INCLUDES_PATH . '/sample-data.php';     // TEMPORARY sample data (no output)

$store      = store_info();
$categories = get_categories();
$featured   = get_featured_products(4);

// Hero background image (optional). If the file is missing, CSS shows a dark gradient.
$heroFile  = 'images/hero/hero-banner.jpg';
$heroStyle = is_file(ROOT_PATH . '/assets/' . $heroFile)
    ? '--hero-image: url("' . asset($heroFile) . '");'
    : '';

$heroTags = ['Custom Stamps', 'Sign Boards', 'Rubber Stamps', 'Company Seal'];

$highlights = [
    ['icon' => 'shield-check', 'title' => 'High Quality',   'text' => 'Long Lasting & Durable'],
    ['icon' => 'clock',        'title' => 'Fast Service',   'text' => 'Quick Turnaround'],
    ['icon' => 'settings',     'title' => 'Custom Design',  'text' => 'As per Your Requirement'],
    ['icon' => 'rupee',        'title' => 'Best Price',     'text' => 'Value for Money'],
];

// Store gallery photos (placeholders appear until real photos are added).
$galleryPhotos = [
    ['file' => 'assets/images/store/store-1.jpg', 'alt' => 'Store photo 1 of 4'],
    ['file' => 'assets/images/store/store-2.jpg', 'alt' => 'Store photo 2 of 4'],
    ['file' => 'assets/images/store/store-3.jpg', 'alt' => 'Store photo 3 of 4'],
    ['file' => 'assets/images/store/store-4.jpg', 'alt' => 'Store photo 4 of 4'],
];

$waUrl   = store_whatsapp_url('Hello, I would like to enquire about your products.');
$mapsUrl = store_maps_url();
?>

<!-- ============================ HERO ============================ -->
<section class="hero" aria-labelledby="hero-heading"<?= $heroStyle !== '' ? ' style="' . e($heroStyle) . '"' : '' ?>>
    <div class="hero-inner">
        <div class="hero-content">
            <ul class="hero-tags" aria-label="What we make">
<?php foreach ($heroTags as $tag): ?>
                <li><?= e($tag) ?></li>
<?php endforeach; ?>
            </ul>

            <h1 class="hero-title" id="hero-heading">
                <span class="hero-title-main">Sourav</span>
                <span class="hero-title-sub">Sign Stamp Store</span>
            </h1>

            <p class="hero-subtitle">
                Professional Stamps &amp; Signage Solutions<br>
                for Your Business &amp; Personal Needs
            </p>

            <a class="btn btn-primary btn-lg" href="<?= e(url('pages/catalog.php')) ?>">
                Explore Catalog <?= icon('chevron-right') ?>
            </a>
        </div>

        <p class="hero-script">Stamped<br>with Trust</p>
    </div>
</section>

<!-- ======================= FEATURE STRIP ======================== -->
<section class="feature-strip" aria-label="Why choose us">
    <ul class="feature-strip-list">
<?php foreach ($highlights as $item): ?>
        <li class="feature-item">
            <span class="feature-icon"><?= icon($item['icon'], 'icon-lg') ?></span>
            <span class="feature-text">
                <strong class="feature-title"><?= e($item['title']) ?></strong>
                <span class="feature-desc"><?= e($item['text']) ?></span>
            </span>
        </li>
<?php endforeach; ?>
    </ul>
</section>

<!-- ================= EXPLORE OUR PRODUCTS (CATEGORIES) ================= -->
<section class="section" aria-labelledby="catalog-heading">
    <div class="container">
        <div class="catalog-panel">
            <div class="catalog-panel-intro">
                <p class="eyebrow">Our Catalog</p>
                <h2 class="section-title" id="catalog-heading">Explore Our Products</h2>
                <p class="catalog-panel-text">
                    From custom rubber stamps to professional sign boards, we provide everything you need under one roof.
                </p>
                <a class="btn btn-outline" href="<?= e(url('pages/catalog.php')) ?>">
                    View Full Catalog <?= icon('chevron-right') ?>
                </a>
            </div>

            <ul class="category-grid">
<?php foreach ($categories as $category): ?>
                <li>
                    <a class="category-card" href="<?= e(url('pages/catalog.php?category=' . rawurlencode($category['slug']))) ?>">
                        <span class="category-card-image">
                            <?= image_or_placeholder($category['image'], $category['name'], 'category-card-img', $category['name']) ?>
                        </span>
                        <span class="category-card-body">
                            <span class="category-card-title"><?= e($category['name']) ?></span>
                            <span class="category-card-sub"><?= e($category['subtitle']) ?></span>
                            <span class="category-card-link">View Products <?= icon('arrow-right') ?></span>
                        </span>
                    </a>
                </li>
<?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- ======================== FEATURED PRODUCTS ======================== -->
<section class="section section-featured" aria-labelledby="featured-heading">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">Popular Picks</p>
                <h2 class="section-title" id="featured-heading">Featured Products</h2>
            </div>
            <a class="link-arrow" href="<?= e(url('pages/catalog.php')) ?>">View All Products <?= icon('arrow-right') ?></a>
        </div>

        <div class="product-grid">
<?php foreach ($featured as $product): ?>
            <?php render_product_card($product); ?>
<?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================ ABOUT ============================ -->
<section class="section" aria-labelledby="about-heading">
    <div class="container">
        <div class="about-intro">
            <div class="about-intro-text">
                <p class="eyebrow">About Us</p>
                <h2 class="section-title" id="about-heading">Your Sign, Our Quality</h2>
                <p>
                    <?= e(STORE_NAME) ?> offers custom stamps, company seals, name plates and sign boards
                    for businesses and individuals. Tell us what you need and choose the product that suits you.
                </p>
                <a class="btn btn-outline" href="<?= e(url('pages/about.php')) ?>">
                    Learn More About Us <?= icon('chevron-right') ?>
                </a>
            </div>

            <div class="about-intro-list">
                <h3 class="about-list-title">What We Offer</h3>
                <ul class="check-list">
<?php foreach ($categories as $category): ?>
                    <li><?= icon('check') ?> <?= e($category['name']) ?></li>
<?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============ WHATSAPP + VISIT STORE + STORE GALLERY ============ -->
<section class="section section-contact-row" aria-labelledby="contact-row-heading">
    <div class="container">
        <h2 class="visually-hidden" id="contact-row-heading">Contact us and visit our store</h2>

        <div class="home-contact-grid">

            <!-- WhatsApp card -->
            <div class="contact-card contact-card-whatsapp">
                <span class="contact-card-icon contact-card-icon-whatsapp"><?= icon('whatsapp', 'icon-lg') ?></span>
                <h3 class="contact-card-title">Chat on WhatsApp</h3>
                <p class="contact-card-text">Get quick quotes, product details or place an order directly.</p>
                <a class="btn btn-whatsapp" href="<?= e($waUrl) ?>"<?= external_link_attrs($waUrl) ?>>
                    Contact Now <?= icon('whatsapp') ?> <?= icon('chevron-right') ?>
                </a>
                <p class="contact-card-phone">
                    <?= icon('phone') ?>
                    <span>
<?php if ($store['phone_tel'] !== ''): ?>
                        <a href="tel:<?= e($store['phone_tel']) ?>"><?= e($store['phone_display']) ?></a>
<?php else: ?>
                        <?= e($store['phone_display']) ?>
<?php endif; ?>
                        <small><?= e($store['availability']) ?></small>
                    </span>
                </p>
            </div>

            <!-- Visit our store card -->
            <div class="contact-card contact-card-visit">
                <span class="contact-card-icon contact-card-icon-pin"><?= icon('map-pin', 'icon-lg') ?></span>
                <h3 class="contact-card-title">Visit Our Store</h3>
                <address class="contact-card-address">
<?php foreach ($store['address_lines'] as $line): ?>
                    <?= e($line) ?><br>
<?php endforeach; ?>
                </address>
                <a class="btn btn-outline" href="<?= e($mapsUrl) ?>"<?= external_link_attrs($mapsUrl) ?>>
                    <?= icon('map') ?> View on Google Maps
                </a>
            </div>

            <!-- Store photo gallery (behaviour added by assets/js/main.js in Batch 5) -->
            <section class="store-gallery" data-gallery aria-labelledby="store-gallery-heading">
                <h3 class="store-gallery-title" id="store-gallery-heading">Our Store</h3>
                <p class="store-gallery-subtitle">Step inside and explore our collection.</p>

                <div class="store-gallery-body">
                    <div class="store-gallery-stage">
                        <button type="button" class="gallery-arrow gallery-arrow-prev" data-gallery-prev aria-label="Previous photo">
                            <?= icon('chevron-left') ?>
                        </button>

                        <div class="gallery-slides" data-gallery-stage aria-live="polite">
<?php foreach ($galleryPhotos as $i => $photo): ?>
                            <figure class="gallery-slide<?= $i === 0 ? ' is-active' : '' ?>" data-gallery-slide>
                                <?= image_or_placeholder($photo['file'], $photo['alt'], 'gallery-img', 'Store photo ' . ($i + 1)) ?>
                            </figure>
<?php endforeach; ?>
                        </div>

                        <button type="button" class="gallery-arrow gallery-arrow-next" data-gallery-next aria-label="Next photo">
                            <?= icon('chevron-right') ?>
                        </button>
                    </div>

                    <ul class="store-gallery-thumbs">
<?php foreach ($galleryPhotos as $i => $photo): ?>
<?php if ($i > 0): ?>
                        <li>
                            <button type="button" class="gallery-thumb" data-gallery-thumb="<?= (int) $i ?>" aria-label="Show photo <?= (int) ($i + 1) ?>">
                                <?= image_or_placeholder($photo['file'], $photo['alt'], 'gallery-thumb-img', (string) ($i + 1)) ?>
                            </button>
                        </li>
<?php endif; ?>
<?php endforeach; ?>
                    </ul>
                </div>

                <div class="store-gallery-dots" role="group" aria-label="Choose a store photo">
<?php foreach ($galleryPhotos as $i => $photo): ?>
                    <button type="button" class="gallery-dot<?= $i === 0 ? ' is-active' : '' ?>" data-gallery-dot="<?= (int) $i ?>" aria-label="Show photo <?= (int) ($i + 1) ?>"></button>
<?php endforeach; ?>
                </div>
            </section>
        </div>

        <?php render_placeholder_notice(); ?>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>