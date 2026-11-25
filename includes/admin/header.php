<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

$adminPublicPage = isset($adminPublicPage) ? (bool) $adminPublicPage : false;

// Authentication gate: runs before any HTML is sent.
if (!$adminPublicPage) {
    require_admin();
}

$pageTitle = isset($pageTitle) ? (string) $pageTitle : 'Admin';
$extraCss  = (isset($extraCss) && is_array($extraCss)) ? $extraCss : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle . ' | Admin | ' . STORE_NAME) ?></title>
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">

    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<?php foreach ($extraCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= e(asset((string) $cssFile)) ?>">
<?php endforeach; ?>
</head>
<body class="admin-body<?= $adminPublicPage ? ' admin-auth-page' : '' ?>">
<?php if ($adminPublicPage): ?>
    <main class="admin-auth" id="main-content">
<?php render_flash(); ?>
<?php else: ?>
    <div class="admin-layout">
<?php require __DIR__ . '/sidebar.php'; ?>

        <div class="admin-main">
            <header class="admin-topbar">
                <h1 class="admin-page-title"><?= e($pageTitle) ?></h1>
                <div class="admin-user">
                    <span class="admin-user-name">Admin: <?= e(admin_username()) ?></span>
                </div>
            </header>

            <main class="admin-content" id="main-content">
<?php render_flash(); ?>
<?php endif; ?>