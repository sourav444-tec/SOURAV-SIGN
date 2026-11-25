<?php
declare(strict_types=1);

/**
 * About page.
 * The business story below is TEMPORARY SAMPLE TEXT (no real facts have been supplied yet).
 * Set $aboutIsPlaceholder = false once the real text has been written.
 */

require_once __DIR__ . '/../config/config.php';
require_once INCLUDES_PATH . '/sample-data.php';   // get_categories() (TEMPORARY sample data)

// Set to false when the placeholder text on this page has been replaced.
$aboutIsPlaceholder = true;

// ---- Page settings read by includes/header.php ----
$pageTitle       = 'About Us';
$metaDescription = 'Learn about ' . STORE_NAME . ': custom stamps, company seals, name plates and sign boards for businesses and individuals.';
$navActive       = 'about';
$bodyClass       = 'page-about';

require_once INCLUDES_PATH . '/header.php';

// Development-only badge shown next to placeholder text (fixed trusted markup).
$showPlaceholderLabels = $aboutIsPlaceholder && APP_ENV !== 'production';
$placeholderBadge      = $showPlaceholderLabels ? '<span class="badge badge-placeholder">Sample text</span>' : '';

$categories = get_categories();

$values = [
    ['icon' => 'shield-check', 'title' => 'High Quality',  'text' => 'We aim to make stamps, seals and boards that are long lasting and durable.'],
    ['icon' => 'clock',        'title' => 'Fast Service',  'text' => 'We aim for a quick turnaround so you are not left waiting.'],
    ['icon' => 'settings',     'title' => 'Custom Design', 'text' => 'Products are made as per your requirement: your text, your logo, your size.'],
    ['icon' => 'rupee',        'title' => 'Best Price',    'text' => 'We aim to give you good value for money on every product.'],
];

$journey = [
    ['title' => 'How We Started',        'text' => 'Sample text: describe here how the shop began and what the first products were.'],
    ['title' => 'How We Grew',           'text' => 'Sample text: describe here how the range, the workshop and the customer base grew over time.'],
    ['title' => 'Where We Are Today',    'text' => 'Sample text: describe here what the shop focuses on today and what you plan next.'],
];

$steps = [
    ['title' => 'Choose a product',   'text' => 'Browse the catalog and pick the product you need.'],
    ['title' => 'Share your details', 'text' => 'Tell us the text, logo, size or design you want.'],
    ['title' => 'We confirm',         'text' => 'We contact you to confirm the product, price and delivery.'],
    ['title' => 'We prepare it',      'text' => 'Your order is prepared as agreed with you.'],
];

$waNumber = store_whatsapp_number();
$waUrl    = store_whatsapp_url('Hello, I would like to know more about your products.');
?>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-title">About Us</h1>
        <p class="page-subtitle">Learn more about <?= e(STORE_NAME) ?> and what we do.</p>
    </div>
</section>

<?php if ($showPlaceholderLabels): ?>
<div class="container">
    <p class="dev-notice" role="note">
        <strong>Preview note:</strong> the story and descriptions on this page are sample text.
        Replace them in <code>pages/about.php</code> and then set <code>$aboutIsPlaceholder = false</code>.
    </p>
</div>
<?php endif; ?>

<!-- ============================ INTRO ============================ -->
<section class="section" aria-labelledby="about-who-heading">
    <div class="container">
        <div class="about-split">
            <div class="about-split-text"<?= $aboutIsPlaceholder ? ' data-placeholder="true"' : '' ?>>
                <p class="eyebrow">Our Story</p>
                <h2 class="section-title" id="about-who-heading">Who We Are <?= $placeholderBadge ?></h2>
                <p>
                    <?= e(STORE_NAME) ?> offers custom stamps, company seals, name plates, sign boards and
                    stamp accessories for businesses and individuals, all under one roof.
                </p>
                <p>
                    Whether you need a self inking stamp for your office, a name plate for your desk or a sign board
                    for your shop, you can tell us what you need and we will help you choose the right product.
                </p>
                <a class="btn btn-primary" href="<?= e(url('pages/catalog.php')) ?>">
                    Explore Catalog <?= icon('chevron-right') ?>
                </a>
            </div>

            <div class="about-split-media">
                <?= image_or_placeholder('assets/images/store/store-1.jpg', 'Inside the ' . STORE_NAME . ' shop', 'about-img', 'Store photo') ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================ JOURNEY ============================ -->
<section class="section section-alt" aria-labelledby="about-journey-heading">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">Our History</p>
                <h2 class="section-title" id="about-journey-heading">Our Journey <?= $placeholderBadge ?></h2>
            </div>
        </div>

        <ol class="story-timeline"<?= $aboutIsPlaceholder ? ' data-placeholder="true"' : '' ?>>
<?php foreach ($journey as $i => $item): ?>
            <li class="story-item">
                <span class="story-item-number" aria-hidden="true"><?= (int) ($i + 1) ?></span>
                <div class="story-item-body">
                    <h3 class="story-item-title"><?= e($item['title']) ?></h3>
                    <p><?= e($item['text']) ?></p>
                </div>
            </li>
<?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ============================ VALUES ============================ -->
<section class="section" aria-labelledby="about-values-heading">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">What We Stand For</p>
                <h2 class="section-title" id="about-values-heading">Our Values</h2>
            </div>
        </div>

        <ul class="value-grid">
<?php foreach ($values as $value): ?>
            <li class="value-card">
                <span class="feature-icon"><?= icon($value['icon'], 'icon-lg') ?></span>
                <h3 class="value-card-title"><?= e($value['title']) ?></h3>
                <p class="value-card-text"><?= e($value['text']) ?></p>
            </li>
<?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- ============================ PRODUCTS ============================ -->
<section class="section section-alt" aria-labelledby="about-products-heading">
    <div class="container">
        <div class="about-split">
            <div class="about-split-text"<?= $aboutIsPlaceholder ? ' data-placeholder="true"' : '' ?>>
                <p class="eyebrow">Product Quality</p>
                <h2 class="section-title" id="about-products-heading">What We Make <?= $placeholderBadge ?></h2>
                <p>
                    Every product is made with care so that stamps give a clear impression and boards and plates
                    look neat and professional. Sample text: describe here the materials, brands and methods you use.
                </p>
            </div>

            <ul class="check-list about-category-list">
<?php foreach ($categories as $category): ?>
                <li>
                    <?= icon('check') ?>
                    <a href="<?= e(url('pages/catalog.php?category=' . rawurlencode($category['slug']))) ?>"><?= e($category['name']) ?></a>
                    <span class="about-category-sub">&ndash; <?= e($category['subtitle']) ?></span>
                </li>
<?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<!-- ============================ HOW WE WORK ============================ -->
<section class="section" aria-labelledby="about-process-heading">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">Simple Process</p>
                <h2 class="section-title" id="about-process-heading">How We Work</h2>
            </div>
        </div>

        <ol class="steps-grid">
<?php foreach ($steps as $i => $step): ?>
            <li class="step-card">
                <span class="step-card-number" aria-hidden="true"><?= (int) ($i + 1) ?></span>
                <h3 class="step-card-title"><?= e($step['title']) ?></h3>
                <p class="step-card-text"><?= e($step['text']) ?></p>
            </li>
<?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- ============================ CALL TO ACTION ============================ -->
<section class="section section-cta" aria-labelledby="about-cta-heading">
    <div class="container">
        <div class="cta-panel">
            <h2 class="section-title" id="about-cta-heading">Have a Question or a Custom Requirement?</h2>
            <p>Get in touch and tell us what you need.</p>
            <div class="cta-actions">
                <a class="btn btn-primary" href="<?= e(url('pages/contact.php')) ?>">Contact Us <?= icon('chevron-right') ?></a>
<?php if ($waNumber !== ''): ?>
                <a class="btn btn-whatsapp" href="<?= e($waUrl) ?>"<?= external_link_attrs($waUrl) ?>><?= icon('whatsapp') ?> Chat on WhatsApp</a>
<?php endif; ?>
                <a class="btn btn-outline" href="<?= e(url('pages/store-location.php')) ?>"><?= icon('map-pin') ?> Visit Our Store</a>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>