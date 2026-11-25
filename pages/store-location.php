<?php
declare(strict_types=1);

/**
 * Store location page.
 * All store details come from includes/store-info.php and are PLACEHOLDER values until replaced.
 *
 * Optional settings in store_info():
 *   'maps_url'       => full https:// Google Maps link  (for the "Open in Google Maps" button)
 *   'maps_embed_url' => the src="" URL from Google Maps > Share > Embed a map (for the map on this page)
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Safe Google Maps embed URL from the store settings, or '' if none/invalid.
 * Only Google's own embed address is accepted.
 */
function location_map_embed_url(): string
{
    $embed = store_clean_url((string) (store_info()['maps_embed_url'] ?? ''));

    return str_starts_with($embed, 'https://www.google.com/maps/embed') ? $embed : '';
}

// ---- Page settings read by includes/header.php ----
$pageTitle       = 'Store Location';
$metaDescription = 'Find the ' . STORE_NAME . ' shop: address, opening hours, contact details and map.';
$navActive       = 'store-location';   // not a navbar link, so no menu item is highlighted
$bodyClass       = 'page-store-location';

require_once INCLUDES_PATH . '/header.php';

// Store details (PLACEHOLDER values) from includes/store-info.php
$store    = store_info();
$embedUrl = location_map_embed_url();
$mapsUrl  = store_clean_url((string) ($store['maps_url'] ?? ''));   // '' = not configured
$waNumber = store_whatsapp_number();
$waUrl    = store_whatsapp_url('Hello, I would like to know more about visiting your store.');
$email    = is_valid_email((string) ($store['email'] ?? '')) ? (string) $store['email'] : '';

// Store photos (placeholders appear until real photos are added).
$storePhotos = [
    ['file' => 'assets/images/store/store-1.jpg', 'alt' => 'Store photo 1 of 4'],
    ['file' => 'assets/images/store/store-2.jpg', 'alt' => 'Store photo 2 of 4'],
    ['file' => 'assets/images/store/store-3.jpg', 'alt' => 'Store photo 3 of 4'],
    ['file' => 'assets/images/store/store-4.jpg', 'alt' => 'Store photo 4 of 4'],
];
?>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-title">Our Store Location</h1>
        <p class="page-subtitle">Visit us at our physical store for direct purchase and consultation.</p>
    </div>
</section>

<section class="section section-location">
    <div class="container">

        <div class="location-layout">

            <!-- ---------------- Address, hours, contact ---------------- -->
            <section class="location-info" aria-labelledby="location-info-heading">
                <h2 class="visually-hidden" id="location-info-heading">Store details</h2>

                <div class="location-block">
                    <span class="contact-card-icon contact-card-icon-pin"><?= icon('map-pin', 'icon-lg') ?></span>
                    <div class="location-block-body">
                        <h3 class="location-block-title">Store Address</h3>
                        <address class="contact-card-address"<?= !empty($store['is_placeholder']) ? ' data-placeholder="true"' : '' ?>>
<?php foreach ($store['address_lines'] as $line): ?>
                            <?= e($line) ?><br>
<?php endforeach; ?>
                        </address>
                    </div>
                </div>

                <div class="location-block">
                    <span class="contact-card-icon"><?= icon('clock', 'icon-lg') ?></span>
                    <div class="location-block-body">
                        <h3 class="location-block-title">Working Hours</h3>
                        <ul class="hours-list">
<?php foreach ($store['hours'] as $row): ?>
                            <li>
                                <span class="hours-day"><?= e($row['label']) ?></span>
                                <span class="hours-time"><?= e($row['time']) ?></span>
                            </li>
<?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="location-block">
                    <span class="contact-card-icon"><?= icon('phone', 'icon-lg') ?></span>
                    <div class="location-block-body">
                        <h3 class="location-block-title">Contact</h3>
                        <ul class="location-contact-list">
                            <li>
<?php if ($store['phone_tel'] !== ''): ?>
                                <a href="tel:<?= e($store['phone_tel']) ?>"><?= e($store['phone_display']) ?></a>
<?php else: ?>
                                <span data-placeholder="true"><?= e($store['phone_display']) ?></span>
<?php endif; ?>
                            </li>
<?php if ($email !== ''): ?>
                            <li><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li>
<?php endif; ?>
                            <li>
<?php if ($waNumber !== ''): ?>
                                <a href="<?= e($waUrl) ?>"<?= external_link_attrs($waUrl) ?>>Chat on WhatsApp</a>
<?php else: ?>
                                <span data-placeholder="true">WhatsApp number not set yet</span>
<?php endif; ?>
                            </li>
                        </ul>
                        <a class="link-arrow" href="<?= e(url('pages/contact.php')) ?>">Send us a message <?= icon('arrow-right') ?></a>
                    </div>
                </div>

                <?php render_placeholder_notice(); ?>
            </section>

            <!-- ---------------- Map ---------------- -->
            <section class="location-map" aria-labelledby="location-map-heading">
                <h2 class="visually-hidden" id="location-map-heading">Map</h2>

<?php if ($embedUrl !== ''): ?>
                <div class="map-embed">
                    <iframe class="map-frame" src="<?= e($embedUrl) ?>" title="Map showing the location of <?= e(STORE_NAME) ?>"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
<?php else: ?>
                <div class="map-placeholder" data-placeholder="true">
                    <?= icon('map', 'icon-xl') ?>
                    <p class="map-placeholder-title">Map not set up yet</p>
                    <p class="map-placeholder-text">The embedded map appears here once the store's Google Maps embed address has been added.</p>
                </div>
<?php endif; ?>

                <div class="map-actions">
<?php if ($mapsUrl !== ''): ?>
                    <a class="btn btn-outline" href="<?= e($mapsUrl) ?>"<?= external_link_attrs($mapsUrl) ?>><?= icon('map') ?> Open in Google Maps</a>
<?php else: ?>
                    <span class="btn btn-outline is-disabled" aria-disabled="true" data-placeholder="true"><?= icon('map') ?> Directions link not set yet</span>
<?php endif; ?>
                </div>
            </section>
        </div>

        <!-- ---------------- Store photos ---------------- -->
        <section class="store-photos" aria-labelledby="store-photos-heading">
            <h2 class="section-title" id="store-photos-heading">Store Photos</h2>
            <ul class="store-photo-grid">
<?php foreach ($storePhotos as $i => $photo): ?>
                <li class="store-photo">
                    <?= image_or_placeholder($photo['file'], $photo['alt'], 'store-photo-img', 'Store photo ' . ($i + 1)) ?>
                </li>
<?php endforeach; ?>
            </ul>
        </section>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>