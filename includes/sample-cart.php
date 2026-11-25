<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * ====================================================================
 * TEMPORARY SAMPLE CART - Phase 2 frontend preview only.
 *
 * The cart, checkout and order-success pages show these sample items so
 * the design can be previewed. Nothing here reads or writes the real
 * session cart or a database.
 *
 * In the cart phase this file is replaced by a real version with the SAME
 * function name, cart_preview_data(), built from cart_items() (session)
 * and get_product() (database). The pages will not need to change.
 *
 * Requires includes/sample-data.php (get_product) to be loaded first.
 * ====================================================================
 */

/**
 * Sample quantities as [productId => quantity].
 *
 * @return array<int, int>
 */
function sample_cart_quantities(): array
{
    return [
        1 => 2,   // Self Inking Stamp x 2
        3 => 1,   // Name Plate (Acrylic) x 1
        5 => 1,   // Stamp Pad x 1
    ];
}

/**
 * Cart lines plus totals.
 *
 * @return array{
 *   lines: array<int, array{product: array<string, mixed>, quantity: int, unit_price: float, line_total: float}>,
 *   subtotal: float,
 *   item_count: int
 * }
 */
function cart_preview_data(bool $empty = false): array
{
    $quantities = $empty ? [] : sample_cart_quantities();

    $lines    = [];
    $subtotal = 0.0;
    $count    = 0;

    foreach ($quantities as $productId => $quantity) {
        $product = get_product((int) $productId);
        if ($product === null) {
            continue;
        }

        $quantity  = max(1, min((int) $quantity, CART_MAX_QTY));
        $unitPrice = (float) $product['price'];
        $lineTotal = $unitPrice * $quantity;

        $lines[] = [
            'product'    => $product,
            'quantity'   => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ];

        $subtotal += $lineTotal;
        $count    += $quantity;
    }

    return [
        'lines'      => $lines,
        'subtotal'   => $subtotal,
        'item_count' => $count,
    ];
}