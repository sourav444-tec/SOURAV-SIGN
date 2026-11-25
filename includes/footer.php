<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

$extraJs    = (isset($extraJs) && is_array($extraJs)) ? $extraJs : [];
$footerLogo = ROOT_PATH . '/assets/images/logo.png';
$socials    = store_social_links();
?>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
<?php if (is_file($footerLogo)): ?>
                <img class="footer-logo" src="<?= e(asset('images/logo.png')) ?>" alt="" width="48" height="48" loading="lazy">
<?php endif; ?>
                <span class="footer-brand-text">
                    <span class="brand-name">Sourav</span>
                    <span class="brand-sub">Sign Stamp Store</span>
                    <span class="brand-tagline"><?= e(STORE_TAGLINE) ?></span>
                </span>
            </div>

            <nav class="footer-links" aria-label="Footer navigation">
                <a href="<?= e(url('')) ?>">Home</a>
                <a href="<?= e(url('pages/catalog.php')) ?>">Catalog</a>
                <a href="<?= e(url('pages/about.php')) ?>">About</a>
                <a href="<?= e(url('pages/contact.php')) ?>">Contact</a>
                <a href="<?= e(url('admin/login.php')) ?>" rel="nofollow">Admin</a>
            </nav>

            <div class="footer-meta">
                <ul class="footer-social" aria-label="Social media">
<?php foreach ($socials as $social): ?>
<?php if ($social['url'] !== ''): ?>
                    <li>
                        <a class="social-link" href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?>">
                            <?= icon($social['icon']) ?>
                        </a>
                    </li>
<?php elseif (APP_ENV !== 'production'): ?>
                    <li>
                        <span class="social-link is-placeholder" title="<?= e($social['label']) ?> link not set yet" aria-hidden="true">
                            <?= icon($social['icon']) ?>
                        </span>
                    </li>
<?php endif; ?>
<?php endforeach; ?>
                </ul>
                <p class="footer-copy">&copy; <?= e(date('Y')) ?> <?= e(STORE_NAME) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

<?php if (is_file(ROOT_PATH . '/assets/js/main.js') || is_file(ROOT_PATH . '/assets/js/main.min.js')): ?>
    <script src="<?= e(asset('js/main.js')) ?>" defer></script>
<?php endif; ?>
<?php foreach ($extraJs as $jsFile): ?>
    <script src="<?= e(asset((string) $jsFile)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>