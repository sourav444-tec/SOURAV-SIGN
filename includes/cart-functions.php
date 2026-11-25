<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

const CART_MAX_QTY   = 99;
const CART_MAX_LINES = 50;

/** Make sure the cart structure exists: [productId => quantity]. */
function cart_init(): void
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Cart contents as [productId => quantity].
 *
 * @return array<int, int>
 */
function cart_items(): array
{
    cart_init();
    $items = [];
    foreach ($_SESSION['cart'] as $id => $qty) {
        $id  = (int) $id;
        $qty = (int) $qty;
        if ($id > 0 && $qty > 0) {
            $items[$id] = min($qty, CART_MAX_QTY);
        }
    }
    return $items;
}

/** Total number of units in the cart (shown in the navbar badge). */
function cart_count(): int
{
    return array_sum(cart_items());
}

/** Number of distinct products in the cart. */
function cart_line_count(): int
{
    return count(cart_items());
}

/**
 * Add units of a product. Returns false for invalid input or a full cart.
 * (Later phases must also verify the product exists and is active in the DB.)
 */
function cart_add(int $productId, int $quantity = 1): bool
{
    cart_init();

    if ($productId < 1 || $quantity < 1) {
        return false;
    }

    $items = cart_items();

    if (!isset($items[$productId]) && count($items) >= CART_MAX_LINES) {
        return false;
    }

    $_SESSION['cart'][$productId] = min(($items[$productId] ?? 0) + $quantity, CART_MAX_QTY);
    return true;
}

/** Set an exact quantity; 0 or less removes the item. */
function cart_set_quantity(int $productId, int $quantity): bool
{
    cart_init();

    if ($productId < 1) {
        return false;
    }
    if ($quantity < 1) {
        return cart_remove($productId);
    }
    if (!isset(cart_items()[$productId])) {
        return false;
    }

    $_SESSION['cart'][$productId] = min($quantity, CART_MAX_QTY);
    return true;
}

function cart_remove(int $productId): bool
{
    cart_init();
    if (!isset($_SESSION['cart'][$productId])) {
        return false;
    }
    unset($_SESSION['cart'][$productId]);
    return true;
}

function cart_clear(): void
{
    $_SESSION['cart'] = [];
}