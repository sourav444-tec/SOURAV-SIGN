<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Inline SVG icon. Names: search, shopping-cart, arrow-right, arrow-left, chevron-right,
 * chevron-left, map-pin, map, phone, mail, clock, shield-check, settings, rupee, check,
 * plus, minus, trash, whatsapp, facebook, instagram, youtube.
 * The returned markup is a fixed trusted string (nothing user-supplied is printed).
 */
function icon(string $name, string $class = ''): string
{
    static $paths = [
        'search'        => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'shopping-cart' => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
        'arrow-right'   => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        'arrow-left'    => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
        'chevron-right' => '<polyline points="9 18 15 12 9 6"/>',
        'chevron-left'  => '<polyline points="15 18 9 12 15 6"/>',
        'map-pin'       => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'map'           => '<polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>',
        'phone'         => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'mail'          => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        'clock'         => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'shield-check'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'settings'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        'rupee'         => '<circle cx="12" cy="12" r="10"/><path d="M8 7h8M8 11h8M8 7h2.5a3 3 0 0 1 0 6H8l6 5"/>',
        'check'         => '<polyline points="20 6 9 17 4 12"/>',
        'plus'          => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'minus'         => '<line x1="5" y1="12" x2="19" y2="12"/>',
        'trash'         => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
        'whatsapp'      => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
        'facebook'      => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
        'instagram'     => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
        'youtube'       => '<path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>',
    ];

    if (!isset($paths[$name])) {
        return '';
    }

    $classes = trim('icon icon-' . $name . ' ' . $class);

    return '<svg class="' . e($classes) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" '
         . 'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" '
         . 'aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
}

/**
 * <img> if the file exists inside the project, otherwise a labelled placeholder box.
 * $relativePath is relative to the project root (e.g. 'assets/images/hero/x.jpg'
 * now, 'uploads/products/x.jpg' once products come from the database).
 */
function image_or_placeholder(string $relativePath, string $alt, string $class = '', string $placeholderLabel = ''): string
{
    $relativePath = ltrim($relativePath, '/');
    $isSafePath   = $relativePath !== ''
        && !str_contains($relativePath, '..')
        && !str_contains($relativePath, '\\');
    $fullPath     = ROOT_PATH . '/' . $relativePath;

    if ($isSafePath && is_file($fullPath)) {
        $src = url($relativePath) . '?v=' . filemtime($fullPath);
        return '<img class="' . e($class) . '" src="' . e($src) . '" alt="' . e($alt) . '" loading="lazy" decoding="async">';
    }

    $label = $placeholderLabel !== '' ? $placeholderLabel : $alt;

    return '<span class="img-placeholder ' . e($class) . '" role="img" aria-label="' . e($alt) . '">'
         . '<span class="img-placeholder-text" aria-hidden="true">' . e($label) . '</span></span>';
}

/** ' target="_blank" rel="noopener noreferrer"' for external (http/https) links, otherwise ''. */
function external_link_attrs(string $href): string
{
    return preg_match('#^https?://#i', $href) ? ' target="_blank" rel="noopener noreferrer"' : '';
}

/**
 * Print one product card. Expects a product array like those from get_products().
 * The "Add to Cart" button is a visual placeholder only in Phase 2.
 *
 * @param array<string, mixed> $product
 */
function render_product_card(array $product): void
{
    $id      = (int) ($product['id'] ?? 0);
    $name    = (string) ($product['name'] ?? '');
    $href    = url('pages/product-details.php?id=' . $id);
    $price   = format_price($product['price'] ?? 0);
    $inStock = !isset($product['in_stock']) || (bool) $product['in_stock'];
    ?>
            <article class="product-card">
                <a class="product-card-image" href="<?= e($href) ?>" tabindex="-1" aria-hidden="true">
                    <?= image_or_placeholder((string) ($product['image'] ?? ''), $name, 'product-card-img', $name) ?>
                </a>
                <div class="product-card-body">
                    <h3 class="product-card-title"><a href="<?= e($href) ?>"><?= e($name) ?></a></h3>
                    <p class="product-card-price"><?= e($price) ?></p>
<?php if ($inStock): ?>
                    <button type="button" class="btn btn-outline btn-block product-card-add"
                            data-demo-action="add-to-cart" data-product-id="<?= $id ?>" data-product-name="<?= e($name) ?>">
                        Add to Cart
                    </button>
<?php else: ?>
                    <button type="button" class="btn btn-outline btn-block product-card-add" disabled>Out of Stock</button>
<?php endif; ?>
                </div>
            </article>
    <?php
}