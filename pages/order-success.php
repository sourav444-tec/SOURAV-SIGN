<?php
declare(strict_types=1);

/**
 * Order success page - VISUAL PREVIEW ONLY (Phase 2).
 * No order is created, saved or sent. In the order phase this page will read the
 * order that was just placed (from the session) and redirect to the catalog when
 * there is none.
 */

require_once __DIR__ . '/../config/config.php';

// FUTURE (order phase): load the just-placed order here; if missing, redirect to the catalog.
$orderNumber = null;   // null = not available in this preview

$pageTitle       = 'Order Confirmation (Preview)';
$metaDescription = 'Order confirmation page preview for ' . STORE_NAME . '.';
$navActive       = 'cart';
$bodyClass       = 'page-order-success';

require_once INCLUDES_PATH . '/header.php';

$waUrl = store_whatsapp_url('Hello, I have a question about my order.');
?>

<section class="section section-success">
    <div class="container container-narrow">

        <p class="dev-notice" role="note">
            <strong>Preview only:</strong> no order has been placed, saved or sent.
            This page only shows how the confirmation will look once ordering is active.
        </p>

        <div class="success-card">
            <span class="success-icon"><?= icon('check', 'icon-xl') ?></span>
            <p class="eyebrow">Order Confirmation</p>
            <h1 class="success-title">Thank You for Your Order</h1>
            <p class="success-text">
                When ordering is live, this page will confirm your order and
                <?= e(STORE_NAME) ?> will contact you to confirm the details.
            </p>

            <dl class="success-details">
                <div class="summary-row">
                    <dt>Order number</dt>
                    <dd><?= $orderNumber !== null ? e((string) $orderNumber) : 'Will appear here after an order is placed' ?></dd>
                </div>
            </dl>

            <section class="success-steps" aria-labelledby="success-steps-heading">
                <h2 class="success-steps-title" id="success-steps-heading">What happens next</h2>
                <ol class="steps-list">
                    <li>We receive your order details.</li>
                    <li>We contact you by phone or WhatsApp to confirm the products, price and delivery.</li>
                    <li>Your order is prepared as agreed with you.</li>
                </ol>
            </section>

            <div class="success-actions">
                <a class="btn btn-primary" href="<?= e(url('pages/catalog.php')) ?>">Continue Shopping</a>
                <a class="btn btn-outline" href="<?= e(url('')) ?>">Back to Home</a>
                <a class="btn btn-whatsapp" href="<?= e($waUrl) ?>"<?= external_link_attrs($waUrl) ?>><?= icon('whatsapp') ?> Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>