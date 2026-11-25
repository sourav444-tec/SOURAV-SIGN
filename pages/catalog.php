<?php
declare(strict_types=1);

/**
 * Product catalog: category filter, sorting, search and product grid.
 * Data comes from includes/sample-data.php (TEMPORARY sample data).
 */

// Config and data are loaded first so the page title can depend on the category.
require_once __DIR__ . '/../config/config.php';
require_once INCLUDES_PATH . '/sample-data.php';

/** Build a catalog URL, leaving out empty/default values. */
function catalog_link(string $category, string $search, string $sort): string
{
    $params = [];
    if ($category !== '') {
        $params['category'] = $category;
    }
    if ($search !== '') {
        $params['q'] = $search;
    }
    if ($sort !== 'popularity') {
        $params['sort'] = $sort;
    }

    $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    return url('pages/catalog.php' . ($query !== '' ? '?' . $query : ''));
}

// ---- Read and validate the URL parameters (never trust them) ----
$categories = get_categories();

$activeSlug = get_text('category', 60);
if (!isset($categories[$activeSlug])) {
    $activeSlug = '';   // unknown or missing category = show all products
}
$activeCategory = $activeSlug !== '' ? $categories[$activeSlug] : null;

$sortOptions = [
    'popularity' => 'Popularity',
    'price-asc'  => 'Price: Low to High',
    'price-desc' => 'Price: High to Low',
    'newest'     => 'Newest First',
    'name'       => 'Name: A to Z',
];
$sort = get_text('sort', 20);
if (!isset($sortOptions[$sort])) {
    $sort = 'popularity';
}

$search = get_text('q', 100);

$products     = get_products(['category' => $activeSlug, 'search' => $search, 'sort' => $sort]);
$productCount = count($products);

// ---- Page settings read by includes/header.php ----
$pageTitle       = $activeCategory !== null ? $activeCategory['name'] . ' – Catalog' : 'Catalog';
$metaDescription = $activeCategory !== null
    ? 'Browse ' . $activeCategory['name'] . ' (' . $activeCategory['subtitle'] . ') from ' . STORE_NAME . '.'
    : 'Browse custom stamps, company seals, name plates, sign boards and stamp accessories from ' . STORE_NAME . '.';
$navActive       = 'catalog';
$bodyClass       = 'page-catalog';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-title"><?= e($activeCategory !== null ? $activeCategory['name'] : 'Our Catalog') ?></h1>
        <p class="page-subtitle">
            <?= e($activeCategory !== null
                ? $activeCategory['subtitle']
                : 'Explore our wide range of sign, stamp and branding solutions.') ?>
        </p>
    </div>
</section>

<section class="section section-catalog">
    <div class="container">
        <div class="catalog-layout">

            <!-- ---------------- Category filter ---------------- -->
            <aside class="catalog-sidebar" aria-labelledby="catalog-categories-heading">
                <h2 class="sidebar-title" id="catalog-categories-heading">Categories</h2>
                <ul class="category-filter">
                    <li>
                        <a class="category-filter-link<?= $activeSlug === '' ? ' active' : '' ?>"
                           href="<?= e(catalog_link('', $search, $sort)) ?>"<?= $activeSlug === '' ? ' aria-current="page"' : '' ?>>All Products</a>
                    </li>
<?php foreach ($categories as $category): ?>
                    <li>
                        <a class="category-filter-link<?= $activeSlug === $category['slug'] ? ' active' : '' ?>"
                           href="<?= e(catalog_link($category['slug'], $search, $sort)) ?>"<?= $activeSlug === $category['slug'] ? ' aria-current="page"' : '' ?>><?= e($category['name']) ?></a>
                    </li>
<?php endforeach; ?>
                </ul>
            </aside>

            <!-- ---------------- Products ---------------- -->
            <div class="catalog-main">

                <div class="catalog-toolbar">
                    <p class="catalog-count" aria-live="polite">
                        Showing <?= (int) $productCount ?> product<?= $productCount === 1 ? '' : 's' ?>
                    </p>

                    <!-- Plain GET form: works without JavaScript. main.js (Batch 5) submits it on change. -->
                    <form class="sort-form" action="<?= e(url('pages/catalog.php')) ?>" method="get" data-auto-submit>
<?php if ($activeSlug !== ''): ?>
                        <input type="hidden" name="category" value="<?= e($activeSlug) ?>">
<?php endif; ?>
<?php if ($search !== ''): ?>
                        <input type="hidden" name="q" value="<?= e($search) ?>">
<?php endif; ?>
                        <label class="sort-label" for="catalog-sort">Sort by:</label>
                        <select class="form-control form-select" id="catalog-sort" name="sort">
<?php foreach ($sortOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>"<?= $sort === $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-outline btn-sm sort-apply">Apply</button>
                    </form>
                </div>

<?php if ($search !== ''): ?>
                <p class="catalog-search-note">
                    Search results for &ldquo;<strong><?= e($search) ?></strong>&rdquo;
                    <a href="<?= e(catalog_link($activeSlug, '', $sort)) ?>">Clear search</a>
                </p>
<?php endif; ?>

<?php if ($productCount > 0): ?>
                <div class="product-grid">
<?php foreach ($products as $product): ?>
                    <?php render_product_card($product); ?>
<?php endforeach; ?>
                </div>
<?php else: ?>
                <div class="empty-state">
                    <h2 class="empty-state-title">No products found</h2>
                    <p class="empty-state-text">We could not find any products matching your selection. Try another category or search.</p>
                    <a class="btn btn-primary" href="<?= e(url('pages/catalog.php')) ?>">View All Products</a>
                </div>
<?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>