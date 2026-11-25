<?php
declare(strict_types=1);

/**
 * Product details page.
 * Data comes from includes/sample-data.php (TEMPORARY sample data).
 * "Add to Cart" is a visual placeholder only in Phase 2.
 */

require_once __DIR__ . '/../config/config.php';
require_once INCLUDES_PATH . '/sample-data.php';

// ---- Find the product (the ID from the URL is validated as an integer) ----
$productId = (int) get_int('id', 0);
$product   = $productId > 0 ? get_product($productId) : null;

if ($product === null) {
    http_response_code(404);   // headers are not sent yet, so this is safe
    $pageTitle       = 'Product Not Found';
    $metaDescription = 'The product you are looking for could not be found.';
} else {
    $pageTitle       = (string) $product['name'];
    $metaDescription = (string) $product['short_description'];
}

$navActive = 'catalog';
$bodyClass = 'page-product';

require_once INCLUDES_PATH . '/header.php';

if ($product === null):
?>
<section class="section">
    <div class="container">
        <div class="empty-state">
            <h1 class="empty-state-title">Product not found</h1>
            <p class="empty-state-text">Sorry, we could not find that product. It may have been removed or the link may be incorrect.</p>
            <a class="btn btn-primary" href="<?= e(url('pages/catalog.php')) ?>">Back to Catalog</a>
        </div>
    </div>
</section>
<?php
    require_once INCLUDES_PATH . '/footer.php';
    return;
endif;

// ---- Prepare the data for the page ----
$category = get_category((string) $product['category']);
$related  = get_related_products($productId, 4);
$inStock  = !isset($product['in_stock']) || (bool) $product['in_stock'];
$features = (isset($product['features']) && is_array($product['features'])) ? $product['features'] : [];

// A product can later supply several images in $product['gallery'] (list of paths).
$gallery = (isset($product['gallery']) && is_array($product['gallery']) && $product['gallery'] !== [])
    ? array_values($product['gallery'])
    : [(string) $product['image']];

/** Public URL of an image if the file exists, otherwise '' (used by the gallery JavaScript). */
$galleryUrl = static function (string $relativePath): string {
    $relativePath = ltrim($relativePath, '/');
    if ($relativePath === '' || str_contains($relativePath, '..') || str_contains($relativePath, '\\')) {
        return '';
    }
    $full = ROOT_PATH . '/' . $relativePath;
    return is_file($full) ? url($relativePath) . '?v=' . filemtime($full) : '';
};

$productName = (string) $product['name'];
?>

<section class="section section-product">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="<?= e(url('')) ?>">Home</a></li>
                <li><a href="<?= e(url('pages/catalog.php')) ?>">Catalog</a></li>
<?php if ($category !== null): ?>
                <li><a href="<?= e(url('pages/catalog.php?category=' . rawurlencode($category['slug']))) ?>"><?= e($category['name']) ?></a></li>
<?php endif; ?>
                <li aria-current="page"><?= e($productName) ?></li>
            </ol>
        </nav>

        <div class="product-layout">

            <!-- ---------------- Gallery ---------------- -->
            <div class="product-gallery" data-product-gallery>
                <ul class="product-gallery-thumbs" aria-label="Product images">
<?php foreach ($gallery as $i => $imagePath): ?>
                    <li>
                        <button type="button" class="gallery-thumb<?= $i === 0 ? ' is-active' : '' ?>"
                                data-gallery-thumb
                                data-image-src="<?= e($galleryUrl((string) $imagePath)) ?>"
                                data-image-alt="<?= e($productName . ' – image ' . ($i + 1)) ?>"
                                aria-label="Show image <?= (int) ($i + 1) ?> of <?= count($gallery) ?>"
                                aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>">
                            <?= image_or_placeholder((string) $imagePath, $productName . ' – thumbnail ' . ($i + 1), 'gallery-thumb-img', (string) ($i + 1)) ?>
                        </button>
                    </li>
<?php endforeach; ?>
                </ul>

                <div class="product-gallery-main" data-gallery-main>
                    <?= image_or_placeholder((string) $gallery[0], $productName, 'product-gallery-main-img', $productName) ?>
                </div>
            </div>

            <!-- ---------------- Product information ---------------- -->
            <div class="product-info">
                <h1 class="product-title"><?= e($productName) ?></h1>
                <p class="product-price"><?= e(format_price($product['price'])) ?></p>
                <p class="product-short"><?= e((string) $product['short_description']) ?></p>

<?php if ($features !== []): ?>
                <ul class="check-list product-features">
<?php foreach ($features as $feature): ?>
                    <li><?= icon('check') ?> <?= e((string) $feature) ?></li>
<?php endforeach; ?>
                </ul>
<?php endif; ?>

                <!--
                    Purchase box. Visual only in Phase 2.
                    In the cart phase this becomes a POST form (with csrf_field()) to an actions/ file.
                -->
                <div class="purchase-box">
<?php if ($inStock): ?>
                    <div class="purchase-row">
                        <label class="form-label" for="product-qty">Quantity</label>
                        <div class="quantity-selector" data-quantity>
                            <button type="button" class="qty-btn" data-qty-minus aria-label="Decrease quantity"><?= icon('minus') ?></button>
                            <input class="qty-input" type="number" id="product-qty" name="quantity" value="1"
                                   min="1" max="<?= (int) CART_MAX_QTY ?>" step="1" inputmode="numeric" data-qty-input>
                            <button type="button" class="qty-btn" data-qty-plus aria-label="Increase quantity"><?= icon('plus') ?></button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary btn-lg product-add-btn"
                            data-demo-action="add-to-cart"
                            data-product-id="<?= (int) $productId ?>"
                            data-product-name="<?= e($productName) ?>"
                            data-qty-source="#product-qty">
                        <?= icon('shopping-cart') ?> Add to Cart
                    </button>
<?php else: ?>
                    <button type="button" class="btn btn-outline btn-lg" disabled>Out of Stock</button>
<?php endif; ?>
                </div>

<?php if ($category !== null): ?>
                <p class="product-meta">
                    Category:
                    <a href="<?= e(url('pages/catalog.php?category=' . rawurlencode($category['slug']))) ?>"><?= e($category['name']) ?></a>
                </p>
<?php endif; ?>
            </div>
        </div>

        <!-- ---------------- Tabs: Description / Specifications ---------------- -->
        <div class="product-tabs" data-tabs>
            <div class="tab-list" role="tablist" aria-label="Product information">
                <button type="button" class="tab-btn is-active" role="tab" id="tab-description"
                        aria-selected="true" aria-controls="panel-description" data-tab-target="panel-description">Description</button>
                <button type="button" class="tab-btn" role="tab" id="tab-specs"
                        aria-selected="false" aria-controls="panel-specs" data-tab-target="panel-specs" tabindex="-1">Specifications</button>
            </div>

            <div class="tab-panel is-active" role="tabpanel" id="panel-description" aria-labelledby="tab-description">
                <p><?= e((string) $product['description']) ?></p>
            </div>

            <div class="tab-panel" role="tabpanel" id="panel-specs" aria-labelledby="tab-specs">
                <table class="spec-table">
                    <tbody>
                        <tr><th scope="row">Product</th><td><?= e($productName) ?></td></tr>
<?php if ($category !== null): ?>
                        <tr><th scope="row">Category</th><td><?= e($category['name']) ?></td></tr>
<?php endif; ?>
                        <tr><th scope="row">Price</th><td><?= e(format_price($product['price'])) ?></td></tr>
                        <tr><th scope="row">Availability</th><td><?= $inStock ? 'In stock' : 'Out of stock' ?></td></tr>
<?php foreach ($features as $feature): ?>
                        <tr><th scope="row">Feature</th><td><?= e((string) $feature) ?></td></tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- ---------------- Related products ---------------- -->
<?php if ($related !== []): ?>
<section class="section section-related" aria-labelledby="related-heading">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="eyebrow">You May Also Like</p>
                <h2 class="section-title" id="related-heading">Related Products</h2>
            </div>
            <a class="link-arrow" href="<?= e(url('pages/catalog.php')) ?>">View All Products <?= icon('arrow-right') ?></a>
        </div>

        <div class="product-grid">
<?php foreach ($related as $item): ?>
            <?php render_product_card($item); ?>
<?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>