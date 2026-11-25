<?php
declare(strict_types=1);

/**
 * Checkout page - INTERFACE ONLY (Phase 2).
 * The form is validated on the server, but NO order is saved, sent or created.
 * Real order processing (MySQL + WhatsApp) is built in a later phase.
 */

require_once __DIR__ . '/../config/config.php';
require_once INCLUDES_PATH . '/sample-data.php';
require_once INCLUDES_PATH . '/sample-cart.php';   // TEMPORARY sample cart

/** ' aria-invalid / aria-describedby' attributes for a field that has an error. */
function checkout_error_attrs(string $key, array $errors): string
{
    return isset($errors[$key]) ? ' aria-invalid="true" aria-describedby="error-' . $key . '"' : '';
}

/** Error message paragraph for a field (already escaped), or ''. */
function checkout_error_html(string $key, array $errors): string
{
    if (!isset($errors[$key])) {
        return '';
    }
    return '<p class="field-error" id="error-' . $key . '">' . e($errors[$key]) . '</p>';
}

$showEmpty = APP_ENV !== 'production' && get_text('preview', 10) === 'empty';
$cart      = cart_preview_data($showEmpty);

// ---- Form handling: validate only; nothing is stored ----
$values      = ['name' => '', 'phone' => '', 'email' => '', 'address' => '', 'city' => '', 'pin' => ''];
$errors      = [];
$formPassed  = false;

if (is_post()) {
    $values['name']    = post_text('name', 100);
    $values['phone']   = post_text('phone', 20);
    $values['email']   = post_text('email', 254);
    $values['address'] = post_text('address', 300, true);
    $values['city']    = post_text('city', 60);
    $values['pin']     = preg_replace('/\s+/', '', post_text('pin', 10)) ?? '';

    if (!csrf_verify(csrf_request_token())) {
        $errors['_form'] = 'Your session expired. Please refresh the page and try again.';
    }
    if (mb_strlen($values['name'], 'UTF-8') < 2) {
        $errors['name'] = 'Please enter your full name.';
    }
    if (!is_valid_phone($values['phone'])) {
        $errors['phone'] = 'Enter a valid 10-digit Indian mobile number.';
    }
    if ($values['email'] !== '' && !is_valid_email($values['email'])) {
        $errors['email'] = 'Enter a valid email address, or leave this blank.';
    }
    if (mb_strlen($values['address'], 'UTF-8') < 10) {
        $errors['address'] = 'Please enter your full address (at least 10 characters).';
    }
    if (mb_strlen($values['city'], 'UTF-8') < 2) {
        $errors['city'] = 'Please enter your city.';
    }
    if (!preg_match('/^[1-9]\d{5}$/', $values['pin'])) {
        $errors['pin'] = 'Enter a valid 6-digit PIN code.';
    }

    $formPassed = ($errors === []);
}

$pageTitle       = 'Checkout';
$metaDescription = 'Enter your delivery details to place an order with ' . STORE_NAME . '.';
$navActive       = 'cart';
$bodyClass       = 'page-checkout';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-title">Checkout</h1>
        <p class="page-subtitle">Enter your details so we can contact you about your order.</p>
    </div>
</section>

<section class="section section-checkout">
    <div class="container">

        <p class="dev-notice" role="note">
            <strong>Preview:</strong> this page shows the checkout design with sample items.
            Submitting does not place or save an order yet.
        </p>

<?php if ($cart['lines'] === []): ?>

        <div class="empty-state">
            <?= icon('shopping-cart', 'icon-xl') ?>
            <h2 class="empty-state-title">Your cart is empty</h2>
            <p class="empty-state-text">Add some products to your cart before checking out.</p>
            <a class="btn btn-primary" href="<?= e(url('pages/catalog.php')) ?>">Browse Catalog</a>
        </div>

<?php else: ?>

        <div class="checkout-layout">

            <!-- ---------------- Customer details form ---------------- -->
            <section class="checkout-card" aria-labelledby="checkout-details-heading">
                <h2 class="checkout-card-title" id="checkout-details-heading">Your Details</h2>

<?php if ($formPassed): ?>
                <div class="flash flash-info" role="status">
                    Your details look valid, but order submission is not active yet in this preview.
                    Nothing has been saved or sent.
                </div>
<?php elseif ($errors !== []): ?>
                <div class="flash flash-error" role="alert">
                    <?= e($errors['_form'] ?? 'Please correct the highlighted fields and try again.') ?>
                </div>
<?php endif; ?>

                <!-- novalidate: the browser's default popups are off; main.js (Batch 5) and PHP show the messages. -->
                <form class="checkout-form" id="checkout-form" action="<?= e(url('pages/checkout.php')) ?>" method="post" novalidate data-validate-form>
                    <?= csrf_field() ?>

                    <div class="form-group<?= isset($errors['name']) ? ' has-error' : '' ?>">
                        <label class="form-label" for="co-name">Full Name <span class="required" aria-hidden="true">*</span></label>
                        <input class="form-control" type="text" id="co-name" name="name" value="<?= e($values['name']) ?>"
                               required minlength="2" maxlength="100" autocomplete="name"<?= checkout_error_attrs('name', $errors) ?>>
                        <?= checkout_error_html('name', $errors) ?>
                    </div>

                    <div class="form-group<?= isset($errors['phone']) ? ' has-error' : '' ?>">
                        <label class="form-label" for="co-phone">Phone Number <span class="required" aria-hidden="true">*</span></label>
                        <input class="form-control" type="tel" id="co-phone" name="phone" value="<?= e($values['phone']) ?>"
                               required maxlength="20" inputmode="tel" autocomplete="tel" data-validate="phone"<?= checkout_error_attrs('phone', $errors) ?>>
                        <?= checkout_error_html('phone', $errors) ?>
                    </div>

                    <div class="form-group<?= isset($errors['email']) ? ' has-error' : '' ?>">
                        <label class="form-label" for="co-email">Email <span class="form-optional">(optional)</span></label>
                        <input class="form-control" type="email" id="co-email" name="email" value="<?= e($values['email']) ?>"
                               maxlength="254" autocomplete="email" data-validate="email"<?= checkout_error_attrs('email', $errors) ?>>
                        <?= checkout_error_html('email', $errors) ?>
                    </div>

                    <div class="form-group<?= isset($errors['address']) ? ' has-error' : '' ?>">
                        <label class="form-label" for="co-address">Address <span class="required" aria-hidden="true">*</span></label>
                        <textarea class="form-control" id="co-address" name="address" rows="3"
                                  required minlength="10" maxlength="300" autocomplete="street-address"<?= checkout_error_attrs('address', $errors) ?>><?= e($values['address']) ?></textarea>
                        <?= checkout_error_html('address', $errors) ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group<?= isset($errors['city']) ? ' has-error' : '' ?>">
                            <label class="form-label" for="co-city">City <span class="required" aria-hidden="true">*</span></label>
                            <input class="form-control" type="text" id="co-city" name="city" value="<?= e($values['city']) ?>"
                                   required minlength="2" maxlength="60" autocomplete="address-level2"<?= checkout_error_attrs('city', $errors) ?>>
                            <?= checkout_error_html('city', $errors) ?>
                        </div>

                        <div class="form-group<?= isset($errors['pin']) ? ' has-error' : '' ?>">
                            <label class="form-label" for="co-pin">PIN Code <span class="required" aria-hidden="true">*</span></label>
                            <input class="form-control" type="text" id="co-pin" name="pin" value="<?= e($values['pin']) ?>"
                                   required maxlength="6" inputmode="numeric" autocomplete="postal-code" data-validate="pin"<?= checkout_error_attrs('pin', $errors) ?>>
                            <?= checkout_error_html('pin', $errors) ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Submit Order</button>
                    <p class="form-note">Order submission is not active yet. In the final version we will contact you to confirm your order.</p>
                </form>
            </section>

            <!-- ---------------- Order summary ---------------- -->
            <aside class="cart-summary checkout-summary" aria-labelledby="checkout-summary-heading">
                <h2 class="cart-summary-title" id="checkout-summary-heading">Order Summary</h2>

                <ul class="summary-items">
<?php foreach ($cart['lines'] as $line): ?>
                    <li class="summary-item">
                        <span class="summary-item-name"><?= e((string) $line['product']['name']) ?> &times; <?= (int) $line['quantity'] ?></span>
                        <span class="summary-item-price"><?= e(format_price($line['line_total'])) ?></span>
                    </li>
<?php endforeach; ?>
                </ul>

                <dl class="summary-list">
                    <div class="summary-row">
                        <dt>Subtotal</dt>
                        <dd><?= e(format_price($cart['subtotal'])) ?></dd>
                    </div>
                    <div class="summary-row">
                        <dt>Delivery / extra charges</dt>
                        <dd>To be confirmed</dd>
                    </div>
                    <div class="summary-row summary-total">
                        <dt>Total</dt>
                        <dd><?= e(format_price($cart['subtotal'])) ?></dd>
                    </div>
                </dl>

                <a class="link-arrow" href="<?= e(url('pages/cart.php')) ?>"><?= icon('arrow-left') ?> Back to Cart</a>
            </aside>
        </div>

<?php endif; ?>

    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>