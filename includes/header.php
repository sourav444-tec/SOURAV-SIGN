<?php
declare(strict_types=1);

// Bootstrap first: starts the session BEFORE any output.
require_once __DIR__ . '/../config/config.php';

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

// Shared helpers for public pages (functions only, no output).
require_once INCLUDES_PATH . '/store-info.php';
require_once INCLUDES_PATH . '/ui-helpers.php';

$pageTitle       = isset($pageTitle) ? (string) $pageTitle : '';
$metaDescription = isset($metaDescription) ? (string) $metaDescription : 'Custom stamps, rubber stamps, name plates and sign boards from ' . STORE_NAME . '.';
$extraCss        = (isset($extraCss) && is_array($extraCss)) ? $extraCss : [];
$bodyClass       = isset($bodyClass) ? (string) $bodyClass : '';
$fullTitle       = $pageTitle !== '' ? $pageTitle . ' | ' . STORE_NAME : STORE_NAME . ' – ' . STORE_TAGLINE;

$faviconFile = ROOT_PATH . '/assets/icons/favicon.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0a">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<?php if (is_file($faviconFile)): ?>
    <link rel="icon" type="image/png" href="<?= e(asset('icons/favicon.png')) ?>">
<?php endif; ?>

    <!-- Fonts: Cinzel (brand), Playfair Display (headings), Great Vibes (script), Poppins (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&amp;family=Great+Vibes&amp;family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,500&amp;family=Poppins:wght@400;500;600&amp;display=swap">

    <!-- Site styles (order matters: responsive.css must come last) -->
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/components.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/responsive.css')) ?>">
<?php foreach ($extraCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= e(asset((string) $cssFile)) ?>">
<?php endforeach; ?>
</head>
<body class="<?= e($bodyClass) ?>">
    <a class="skip-link" href="#main-content">Skip to main content</a>

<?php require __DIR__ . '/navbar.php'; ?>

    <main id="main-content">
<?php render_flash(); ?>