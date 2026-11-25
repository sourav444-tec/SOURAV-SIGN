<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

if (!isset($adminActive)) {
    $adminActive = current_page();
}

$adminMenu = [
    'dashboard'       => ['label' => 'Dashboard',        'href' => url('admin/dashboard.php')],
    'products'        => ['label' => 'Products',         'href' => url('admin/products.php')],
    'orders'          => ['label' => 'Orders',           'href' => url('admin/orders.php')],
    'customers'       => ['label' => 'Customers',        'href' => url('admin/customers.php')],
    'gallery'         => ['label' => 'Gallery',          'href' => url('admin/gallery.php')],
    'settings'        => ['label' => 'Website Settings', 'href' => url('admin/settings.php')],
    'change-password' => ['label' => 'Change Password',  'href' => url('admin/change-password.php')],
];
?>
        <aside class="admin-sidebar" aria-label="Admin navigation">
            <a class="admin-brand" href="<?= e(url('admin/dashboard.php')) ?>">
                <span class="brand-name">Sourav</span>
                <span class="brand-sub">Sign Stamp Store</span>
            </a>

            <nav>
                <ul class="admin-menu">
<?php foreach ($adminMenu as $key => $item): ?>
                    <li>
                        <a href="<?= e($item['href']) ?>" class="admin-menu-link<?= $adminActive === $key ? ' active' : '' ?>"<?= $adminActive === $key ? ' aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
                    </li>
<?php endforeach; ?>
                    <li>
                        <form action="<?= e(url('actions/admin/logout.php')) ?>" method="post" class="admin-logout-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="admin-menu-link admin-logout-btn">Logout</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>