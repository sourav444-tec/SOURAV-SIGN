<?php
declare(strict_types=1);

/**
 * Shopping cart page - VISUAL PREVIEW ONLY (Phase 2).
 * Shows sample items from includes/sample-cart.php. The real session cart,
 * quantity updates and removal are built in a later phase.
 */

require_once __DIR__ . '/../config/config.php';
require_once INCLUDES_PATH . '/sample-data.php';
require_once INCLUDES_PATH . '/sample-cart.php';   // TEMPORARY sample cart

// Development-only shortcut to preview the empty-cart design: cart.php?preview=empty
$showEmpty = APP_ENV !== 'production' && get_text('preview', 10) === 'empty';
$cart      = cart_preview_data($showEmpty);

$pageTitle       = 'Shopping Cart';
$metaDescription = 'Review the items in your shopping cart at ' . STORE_NAME . '.';
$navActive       = 'cart';
$bodyClass       = 'page-cart';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        <p class="page-subtitle">Review your selected products before checkout.</p>
    </div>
</section>

<section class="section section-cart">
    <div class="container">

        <p class="dev-notice" role="note">
            <strong>Preview:</strong> the items below are sample data to show the cart design.
            Adding, changing and removing items is not active yet.
        </p>

<?php if ($cart['lines'] === []): ?>

        <div class="empty-state">
            <?= icon('shopping-cart', 'icon-xl') ?>
            <h2 class="empty-state-title">Your cart is empty</h2>
            <p class="empty-state-text">Browse our catalog and add products to your cart.</p>
            <a class="btn btn-primary" href="<?= e(url('pages/catalog.php')) ?>">Continue Shopping</a>
        </div>

<?php else: ?>

        <div class="cart-layout">

            <!-- ---------------- Cart items ---------------- -->
            <section class="cart-items" aria-labelledby="cart-items-heading">
                <h2 class="visually-hidden" id="cart-items-heading">Items in your cart</h2>

                <div class="cart-list-head" aria-hidden="true">
                    <span>Product</span>
                    <span>Price</span>
                    <span>Quantity</span>
                    <span>Subtotal</span>
                    <span></span>
                </div>

                <ul class="cart-list">
<?php foreach ($cart['lines'] as $line):
    $product   = $line['product'];
    $pid       = (int) $product['id'];
    $name      = (string) $product['name'];
    $href      = url('pages/product-details.php?id=' . $pid);
    $lineCat   = get_category((string) $product['category']);
?>
                    <li class="cart-item" data-cart-item data-product-id="<?= $pid ?>" data-unit-price="<?= e((string) $line['unit_price']) ?>">
                        <a class="cart-item-image" href="<?= e($href) ?>" tabindex="-1" aria-hidden="true">
                            <?= image_or_placeholder((string) $product['image'], $name, 'cart-item-img', $name) ?>
                        </a>

                        <div class="cart-item-info">
                            <h3 class="cart-item-title"><a href="<?= e($href) ?>"><?= e($name) ?></a></h3>
<?php if ($lineCat !== null): ?>
                            <p class="cart-item-category"><?= e($lineCat['name']) ?></p>
<?php endif; ?>
                        </div>

                        <p class="cart-item-price" data-label="Price"><?= e(format_price($line['unit_price'])) ?></p>

                        <div class="cart-item-qty" data-label="Quantity">
                            <label class="visually-hidden" for="cart-qty-<?= $pid ?>">Quantity for <?= e($name) ?></label>
                            <div class="quantity-selector" data-quantity>
                                <button type="button" class="qty-btn" data-qty-minus aria-label="Decrease quantity of <?= e($name) ?>"><?= icon('minus') ?></button>
                                <input class="qty-input" type="number" id="cart-qty-<?= $pid ?>" value="<?= (int) $line['quantity'] ?>"
                                       min="1" max="<?= (int) CART_MAX_QTY ?>" step="1" inputmode="numeric" data-qty-input>
                                <button type="button" class="qty-btn" data-qty-plus aria-label="Increase quantity of <?= e($name) ?>"><?= icon('plus') ?></button>
                            </div>
                        </div>

                        <p class="cart-item-subtotal" data-label="Subtotal">
                            <span data-line-total><?= e(format_price($line['line_total'])) ?></span>
                        </p>

                        <button type="button" class="cart-item-remove" data-demo-action="remove-from-cart"
                                data-product-id="<?= $pid ?>" aria-label="Remove <?= e($name) ?> from cart">
                            <?= icon('trash') ?>
                        </button>
                    </li>
<?php endforeach; ?>
                </ul>

                <a class="link-arrow cart-continue" href="<?= e(url('pages/catalog.php')) ?>"><?= icon('arrow-left') ?> Continue Shopping</a>
            </section>

            <!-- ---------------- Summary ---------------- -->
            <aside class="cart-summary" aria-labelledby="cart-summary-heading">
                <h2 class="cart-summary-title" id="cart-summary-heading">Order Summary</h2>

                <dl class="summary-list">
                    <div class="summary-row">
                        <dt>Items</dt>
                        <dd data-cart-item-count><?= (int) $cart['item_count'] ?></dd>
                    </div>
                    <div class="summary-row">
                        <dt>Subtotal</dt>
                        <dd data-cart-subtotal><?= e(format_price($cart['subtotal'])) ?></dd>
                    </div>
                    <div class="summary-row">
                        <dt>Delivery / extra charges</dt>
                        <dd>To be confirmed</dd>
                    </div>
                    <div class="summary-row summary-total">
                        <dt>Total</dt>
                        <dd data-cart-total><?= e(format_price($cart['subtotal'])) ?></dd>
                    </div>
                </dl>

                <a class="btn btn-primary btn-lg btn-block" href="<?= e(url('pages/checkout.php')) ?>">
                    Proceed to Checkout <?= icon('chevron-right') ?>
                </a>
            </aside>
        </div>

<?php endif; ?>

    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>