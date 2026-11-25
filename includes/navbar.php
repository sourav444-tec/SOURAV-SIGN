<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

// Work out the active link: page can set $navActive, otherwise guess from filename.
if (!isset($navActive)) {
    $navActive = current_page() === 'index' ? 'home' : current_page();
}

$navLinks = [
    'home'    => ['label' => 'Home',    'href' => url('')],
    'catalog' => ['label' => 'Catalog', 'href' => url('pages/catalog.php')],
    'about'   => ['label' => 'About',   'href' => url('pages/about.php')],
    'contact' => ['label' => 'Contact', 'href' => url('pages/contact.php')],
];

$logoFile = ROOT_PATH . '/assets/images/logo.png';
$cartQty  = cart_count();
?>
    <header class="site-header">
        <nav class="navbar" aria-label="Main navigation">
            <a class="navbar-brand" href="<?= e(url('')) ?>">
<?php if (is_file($logoFile)): ?>
                <img class="navbar-logo" src="<?= e(asset('images/logo.png')) ?>" alt="" width="48" height="48">
<?php endif; ?>
                <span class="navbar-brand-text">
                    <span class="brand-name">Sourav</span>
                    <span class="brand-sub">Sign Stamp Store</span>
                    <span class="brand-tagline"><?= e(STORE_TAGLINE) ?></span>
                </span>
            </a>

            <button class="navbar-toggle" type="button" aria-expanded="false" aria-controls="navbar-menu" aria-label="Toggle navigation menu">
                <span class="navbar-toggle-bar"></span>
                <span class="navbar-toggle-bar"></span>
                <span class="navbar-toggle-bar"></span>
            </button>

            <div class="navbar-menu" id="navbar-menu">
                <ul class="navbar-links">
<?php foreach ($navLinks as $key => $link): ?>
                    <li>
                        <a href="<?= e($link['href']) ?>" class="nav-link<?= $navActive === $key ? ' active' : '' ?>"<?= $navActive === $key ? ' aria-current="page"' : '' ?>><?= e($link['label']) ?></a>
                    </li>
<?php endforeach; ?>
                </ul>

                <form class="navbar-search" action="<?= e(url('pages/catalog.php')) ?>" method="get" role="search">
                    <label class="visually-hidden" for="nav-search">Search products</label>
                    <button type="submit" class="navbar-search-btn" aria-label="Search"><?= icon('search') ?></button>
                    <input type="search" id="nav-search" name="q" placeholder="Search products..." maxlength="100"
                           value="<?= e(get_text('q', 100)) ?>" autocomplete="off">
                </form>

                <a class="navbar-cart" href="<?= e(url('pages/cart.php')) ?>" aria-label="Shopping cart, <?= (int) $cartQty ?> items">
                    <?= icon('shopping-cart', 'icon-lg') ?>
                    <span class="cart-count" data-cart-count><?= (int) $cartQty ?></span>
                </a>
            </div>
        </nav>
    </header>