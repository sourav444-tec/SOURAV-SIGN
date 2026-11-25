<?php
declare(strict_types=1);

/**
 * Contact page - INTERFACE ONLY (Phase 2).
 * The enquiry form is validated on the server, but NO message is saved, emailed or sent.
 * Real enquiry handling is built in a later phase.
 * Store details come from includes/store-info.php (PLACEHOLDER values).
 */

require_once __DIR__ . '/../config/config.php';

/** ' aria-invalid / aria-describedby' attributes for a field that has an error. */
function contact_error_attrs(string $key, array $errors): string
{
    return isset($errors[$key]) ? ' aria-invalid="true" aria-describedby="error-' . $key . '"' : '';
}

/** Error message paragraph for a field (already escaped), or ''. */
function contact_error_html(string $key, array $errors): string
{
    if (!isset($errors[$key])) {
        return '';
    }
    return '<p class="field-error" id="error-' . $key . '">' . e($errors[$key]) . '</p>';
}

// ---- Form handling: validate only; nothing is stored or sent ----
$values     = ['name' => '', 'phone' => '', 'email' => '', 'message' => ''];
$errors     = [];
$formPassed = false;

if (is_post()) {
    $values['name']    = post_text('name', 100);
    $values['phone']   = post_text('phone', 20);
    $values['email']   = post_text('email', 254);
    $values['message'] = post_text('message', 1000, true);
    $honeypot          = post_text('website', 100);   // hidden field: real visitors leave it empty

    if (!csrf_verify(csrf_request_token())) {
        $errors['_form'] = 'Your session expired. Please refresh the page and try again.';
    } elseif ($honeypot !== '') {
        $errors['_form'] = 'We could not process this form. Please try again.';
    }
    if (mb_strlen($values['name'], 'UTF-8') < 2) {
        $errors['name'] = 'Please enter your name.';
    }
    if (!is_valid_phone($values['phone'])) {
        $errors['phone'] = 'Enter a valid 10-digit Indian mobile number.';
    }
    if ($values['email'] !== '' && !is_valid_email($values['email'])) {
        $errors['email'] = 'Enter a valid email address, or leave this blank.';
    }
    if (mb_strlen($values['message'], 'UTF-8') < 10) {
        $errors['message'] = 'Please enter your message (at least 10 characters).';
    }

    $formPassed = ($errors === []);
}

// ---- Page settings read by includes/header.php ----
$pageTitle       = 'Contact Us';
$metaDescription = 'Contact ' . STORE_NAME . ' by phone, WhatsApp or the enquiry form for quotes, product details and orders.';
$navActive       = 'contact';
$bodyClass       = 'page-contact';

require_once INCLUDES_PATH . '/header.php';

// Store details (PLACEHOLDER values) from includes/store-info.php
$store    = store_info();
$waNumber = store_whatsapp_number();
$waUrl    = store_whatsapp_url('Hello, I would like to enquire about your products.');
$email    = is_valid_email((string) ($store['email'] ?? '')) ? (string) $store['email'] : '';
?>

<!-- ========================= PAGE BANNER ========================= -->
<section class="page-banner">
    <div class="container">
        <h1 class="page-title">Get In Touch</h1>
        <p class="page-subtitle">We are always here to help you with your queries and orders.</p>
    </div>
</section>

<section class="section section-contact">
    <div class="container">

        <p class="dev-notice" role="note">
            <strong>Preview:</strong> the enquiry form is checked on the server but does not send or save your message yet.
        </p>

        <div class="contact-layout">

            <!-- ---------------- Contact details ---------------- -->
            <section class="contact-details" aria-labelledby="contact-details-heading">
                <h2 class="contact-card-title" id="contact-details-heading">Contact Details</h2>

                <!-- WhatsApp -->
                <div class="contact-method contact-method-whatsapp">
                    <span class="contact-card-icon contact-card-icon-whatsapp"><?= icon('whatsapp', 'icon-lg') ?></span>
                    <div class="contact-method-body">
                        <h3 class="contact-method-title">WhatsApp Us</h3>
                        <p class="contact-method-text">Chat with us for quick support, product details or to place an order directly.</p>
<?php if ($waNumber !== ''): ?>
                        <a class="btn btn-whatsapp" href="<?= e($waUrl) ?>"<?= external_link_attrs($waUrl) ?>>
                            Chat on WhatsApp <?= icon('chevron-right') ?>
                        </a>
<?php else: ?>
                        <span class="btn btn-whatsapp is-disabled" aria-disabled="true" data-placeholder="true">WhatsApp number not set yet</span>
<?php endif; ?>
                    </div>
                </div>

                <!-- Phone -->
                <div class="contact-method">
                    <span class="contact-card-icon"><?= icon('phone', 'icon-lg') ?></span>
                    <div class="contact-method-body">
                        <h3 class="contact-method-title">Call Us</h3>
                        <p class="contact-method-text">
<?php if ($store['phone_tel'] !== ''): ?>
                            <a href="tel:<?= e($store['phone_tel']) ?>"><?= e($store['phone_display']) ?></a>
<?php else: ?>
                            <span data-placeholder="true"><?= e($store['phone_display']) ?></span>
<?php endif; ?>
                            <br><small><?= e($store['availability']) ?></small>
                        </p>
                    </div>
                </div>

<?php if ($email !== ''): ?>
                <!-- Email (shown only when a valid email is configured) -->
                <div class="contact-method">
                    <span class="contact-card-icon"><?= icon('mail', 'icon-lg') ?></span>
                    <div class="contact-method-body">
                        <h3 class="contact-method-title">Email Us</h3>
                        <p class="contact-method-text"><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
                    </div>
                </div>
<?php endif; ?>

                <!-- Address -->
                <div class="contact-method">
                    <span class="contact-card-icon contact-card-icon-pin"><?= icon('map-pin', 'icon-lg') ?></span>
                    <div class="contact-method-body">
                        <h3 class="contact-method-title">Visit Our Store</h3>
                        <address class="contact-card-address">
<?php foreach ($store['address_lines'] as $line): ?>
                            <?= e($line) ?><br>
<?php endforeach; ?>
                        </address>
                        <a class="link-arrow" href="<?= e(url('pages/store-location.php')) ?>">Store location and map <?= icon('arrow-right') ?></a>
                    </div>
                </div>

                <!-- Opening hours -->
                <div class="contact-method">
                    <span class="contact-card-icon"><?= icon('clock', 'icon-lg') ?></span>
                    <div class="contact-method-body">
                        <h3 class="contact-method-title">Working Hours</h3>
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

                <?php render_placeholder_notice(); ?>
            </section>

            <!-- ---------------- Enquiry form ---------------- -->
            <section class="contact-form-card" aria-labelledby="contact-form-heading">
                <h2 class="contact-card-title" id="contact-form-heading">Send Us a Message</h2>

<?php if ($formPassed): ?>
                <div class="flash flash-info" role="status">
                    Your message looks valid, but sending is not active yet in this preview.
                    Nothing has been saved or sent.
                </div>
<?php elseif ($errors !== []): ?>
                <div class="flash flash-error" role="alert">
                    <?= e($errors['_form'] ?? 'Please correct the highlighted fields and try again.') ?>
                </div>
<?php endif; ?>

                <!-- novalidate: browser popups are off; main.js (Batch 5) and PHP show the messages. -->
                <form class="contact-form" id="contact-form" action="<?= e(url('pages/contact.php')) ?>" method="post" novalidate data-validate-form>
                    <?= csrf_field() ?>

                    <!-- Honeypot: hidden from people, CSS (Batch 4) moves it off-screen. Must stay empty. -->
                    <div class="form-hp" aria-hidden="true">
                        <label for="ct-website">Leave this field empty</label>
                        <input type="text" id="ct-website" name="website" value="" tabindex="-1" autocomplete="off" data-honeypot>
                    </div>

                    <div class="form-group<?= isset($errors['name']) ? ' has-error' : '' ?>">
                        <label class="form-label" for="ct-name">Your Name <span class="required" aria-hidden="true">*</span></label>
                        <input class="form-control" type="text" id="ct-name" name="name" value="<?= e($values['name']) ?>"
                               required minlength="2" maxlength="100" autocomplete="name"<?= contact_error_attrs('name', $errors) ?>>
                        <?= contact_error_html('name', $errors) ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group<?= isset($errors['phone']) ? ' has-error' : '' ?>">
                            <label class="form-label" for="ct-phone">Phone Number <span class="required" aria-hidden="true">*</span></label>
                            <input class="form-control" type="tel" id="ct-phone" name="phone" value="<?= e($values['phone']) ?>"
                                   required maxlength="20" inputmode="tel" autocomplete="tel" data-validate="phone"<?= contact_error_attrs('phone', $errors) ?>>
                            <?= contact_error_html('phone', $errors) ?>
                        </div>

                        <div class="form-group<?= isset($errors['email']) ? ' has-error' : '' ?>">
                            <label class="form-label" for="ct-email">Email <span class="form-optional">(optional)</span></label>
                            <input class="form-control" type="email" id="ct-email" name="email" value="<?= e($values['email']) ?>"
                                   maxlength="254" autocomplete="email" data-validate="email"<?= contact_error_attrs('email', $errors) ?>>
                            <?= contact_error_html('email', $errors) ?>
                        </div>
                    </div>

                    <div class="form-group<?= isset($errors['message']) ? ' has-error' : '' ?>">
                        <label class="form-label" for="ct-message">Your Message <span class="required" aria-hidden="true">*</span></label>
                        <textarea class="form-control" id="ct-message" name="message" rows="5"
                                  required minlength="10" maxlength="1000" data-char-counter="1000"<?= contact_error_attrs('message', $errors) ?>><?= e($values['message']) ?></textarea>
                        <?= contact_error_html('message', $errors) ?>
                        <p class="form-hint">Maximum 1000 characters. <span data-char-counter-output aria-live="polite"></span></p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Send Message</button>
                    <p class="form-note">Sending is not active yet. For now, please call or use WhatsApp once the number is available.</p>
                </form>
            </section>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>