<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * ====================================================================
 * TEMPORARY SAMPLE DATA - Phase 2 frontend preview only.
 *
 * No database is used here. In the database phase this whole file will be
 * replaced by a MySQL/PDO version that keeps the SAME function names:
 *   get_categories(), get_category(), get_products(), get_product(),
 *   get_featured_products(), get_related_products()
 * so the customer pages do not need to change.
 *
 * Image paths are relative to the project root. Missing images show a
 * placeholder box automatically.
 * ====================================================================
 */

/**
 * All categories, keyed by slug.
 *
 * @return array<string, array{slug:string, name:string, subtitle:string, image:string}>
 */
function get_categories(): array
{
    return [
        'custom-stamps' => [
            'slug' => 'custom-stamps', 'name' => 'Custom Stamps',
            'subtitle' => 'Self Inking / Rubber / Dater',
            'image' => 'assets/images/categories/custom-stamps.jpg',
        ],
        'company-seal' => [
            'slug' => 'company-seal', 'name' => 'Company Seal',
            'subtitle' => 'Round / Square / Custom',
            'image' => 'assets/images/categories/company-seal.jpg',
        ],
        'sign-boards' => [
            'slug' => 'sign-boards', 'name' => 'Sign Boards',
            'subtitle' => 'Name Plate / Office / Shop',
            'image' => 'assets/images/categories/sign-boards.jpg',
        ],
        'stamp-accessories' => [
            'slug' => 'stamp-accessories', 'name' => 'Stamp Accessories',
            'subtitle' => 'Ink / Pad / Refill / Others',
            'image' => 'assets/images/categories/stamp-accessories.jpg',
        ],
        'name-plates' => [
            'slug' => 'name-plates', 'name' => 'Name Plates',
            'subtitle' => 'Metal / Acrylic / Wooden',
            'image' => 'assets/images/categories/name-plates.jpg',
        ],
    ];
}

/** One category by slug, or null. */
function get_category(string $slug): ?array
{
    return get_categories()[$slug] ?? null;
}

/**
 * The raw sample product list (internal; use the get_* functions below).
 *
 * @return array<int, array<string, mixed>>
 */
function sample_products_raw(): array
{
    return [
        [
            'id' => 1, 'name' => 'Self Inking Stamp', 'category' => 'custom-stamps', 'price' => 450,
            'short_description' => 'Self inking stamp with a clear, sharp impression.',
            'description' => 'Our self inking stamps are designed for long-lasting use with a clean and professional impression. Perfect for offices, businesses and personal use. Custom sizes and designs available.',
            'features' => ['High quality self inking mechanism', 'Clear and sharp impression', 'Available in multiple sizes', 'Custom text / logo available'],
            'image' => 'assets/images/products/self-inking-stamp.jpg',
            'in_stock' => true, 'popularity' => 95, 'featured' => true, 'added' => '2026-10-01',
        ],
        [
            'id' => 2, 'name' => 'Rubber Stamp', 'category' => 'custom-stamps', 'price' => 250,
            'short_description' => 'Classic rubber stamp made to your text or design.',
            'description' => 'A classic rubber stamp made with your own text or design. A simple, practical choice for everyday office and personal use.',
            'features' => ['Custom text / logo available', 'Practical for everyday use'],
            'image' => 'assets/images/products/rubber-stamp.jpg',
            'in_stock' => true, 'popularity' => 80, 'featured' => true, 'added' => '2026-10-03',
        ],
        [
            'id' => 3, 'name' => 'Name Plate (Acrylic)', 'category' => 'name-plates', 'price' => 350,
            'short_description' => 'Acrylic name plate for desks, doors and offices.',
            'description' => 'An acrylic name plate for desks, doors and offices, made with the name and details you choose.',
            'features' => ['Custom name and details', 'Suitable for office and home'],
            'image' => 'assets/images/products/name-plate-acrylic.jpg',
            'in_stock' => true, 'popularity' => 85, 'featured' => true, 'added' => '2026-10-05',
        ],
        [
            'id' => 4, 'name' => 'Sign Board', 'category' => 'sign-boards', 'price' => 1200,
            'short_description' => 'Sign board for shops and offices.',
            'description' => 'A sign board for shops and offices, made to your requirement. Contact us to discuss size and design.',
            'features' => ['Made as per your requirement', 'Suitable for shops and offices'],
            'image' => 'assets/images/products/sign-board.jpg',
            'in_stock' => true, 'popularity' => 70, 'featured' => true, 'added' => '2026-10-08',
        ],
        [
            'id' => 5, 'name' => 'Stamp Pad', 'category' => 'stamp-accessories', 'price' => 150,
            'short_description' => 'Ink pad for rubber stamps.',
            'description' => 'An ink pad for use with rubber stamps.',
            'features' => ['Works with rubber stamps'],
            'image' => 'assets/images/products/stamp-pad.jpg',
            'in_stock' => true, 'popularity' => 60, 'featured' => false, 'added' => '2026-10-10',
        ],
        [
            'id' => 6, 'name' => 'Date Stamp', 'category' => 'custom-stamps', 'price' => 400,
            'short_description' => 'Stamp with an adjustable date.',
            'description' => 'A stamp with an adjustable date, useful for receipts, files and office records.',
            'features' => ['Adjustable date', 'Useful for office records'],
            'image' => 'assets/images/products/date-stamp.jpg',
            'in_stock' => true, 'popularity' => 65, 'featured' => false, 'added' => '2026-10-12',
        ],
        [
            'id' => 7, 'name' => 'Company Seal (Round)', 'category' => 'company-seal', 'price' => 550,
            'short_description' => 'Round company seal with your company name.',
            'description' => 'A round company seal made with your company name and details.',
            'features' => ['Round design', 'Custom company details'],
            'image' => 'assets/images/products/company-seal-round.jpg',
            'in_stock' => true, 'popularity' => 55, 'featured' => false, 'added' => '2026-10-15',
        ],
    ];
}

/**
 * Products with optional filtering and sorting.
 *
 * $filters keys (all optional):
 *   'category' => category slug
 *   'search'   => text to look for in name / short description
 *   'sort'     => 'popularity' (default) | 'price-asc' | 'price-desc' | 'newest' | 'name'
 *
 * @param array<string, mixed> $filters
 * @return array<int, array<string, mixed>>
 */
function get_products(array $filters = []): array
{
    $items = sample_products_raw();

    $category = (string) ($filters['category'] ?? '');
    if ($category !== '') {
        $items = array_filter($items, static fn (array $p): bool => $p['category'] === $category);
    }

    $search = trim((string) ($filters['search'] ?? ''));
    if ($search !== '') {
        $items = array_filter(
            $items,
            static fn (array $p): bool => mb_stripos($p['name'] . ' ' . $p['short_description'], $search, 0, 'UTF-8') !== false
        );
    }

    $items = array_values($items);

    $sort = (string) ($filters['sort'] ?? 'popularity');
    usort($items, match ($sort) {
        'price-asc'  => static fn (array $a, array $b): int => $a['price'] <=> $b['price'],
        'price-desc' => static fn (array $a, array $b): int => $b['price'] <=> $a['price'],
        'newest'     => static fn (array $a, array $b): int => strcmp((string) $b['added'], (string) $a['added']),
        'name'       => static fn (array $a, array $b): int => strcasecmp((string) $a['name'], (string) $b['name']),
        default      => static fn (array $a, array $b): int => $b['popularity'] <=> $a['popularity'],
    });

    return $items;
}

/** One product by ID, or null. */
function get_product(int $id): ?array
{
    foreach (sample_products_raw() as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

/**
 * Featured products (most popular first).
 *
 * @return array<int, array<string, mixed>>
 */
function get_featured_products(int $limit = 4): array
{
    $featured = array_values(array_filter(
        get_products(['sort' => 'popularity']),
        static fn (array $p): bool => !empty($p['featured'])
    ));

    return array_slice($featured, 0, max(0, $limit));
}

/**
 * Related products: same category first, then other popular products.
 *
 * @return array<int, array<string, mixed>>
 */
function get_related_products(int $productId, int $limit = 4): array
{
    $current = get_product($productId);
    if ($current === null) {
        return [];
    }

    $others = array_values(array_filter(
        get_products(['sort' => 'popularity']),
        static fn (array $p): bool => $p['id'] !== $productId
    ));

    // Same category first (stable order otherwise).
    usort($others, static function (array $a, array $b) use ($current): int {
        return ((int) ($b['category'] === $current['category'])) <=> ((int) ($a['category'] === $current['category']));
    });

    return array_slice($others, 0, max(0, $limit));
}