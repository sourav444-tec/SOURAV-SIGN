<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

$adminPublicPage = isset($adminPublicPage) ? (bool) $adminPublicPage : false;
$extraJs         = (isset($extraJs) && is_array($extraJs)) ? $extraJs : [];
?>
<?php if ($adminPublicPage): ?>
    </main>
<?php else: ?>
            </main>

            <footer class="admin-footer">
                <p>&copy; <?= e(date('Y')) ?> <?= e(STORE_NAME) ?> &middot; Admin Panel</p>
            </footer>
        </div><!-- /.admin-main -->
    </div><!-- /.admin-layout -->
<?php endif; ?>

<?php if (is_file(ROOT_PATH . '/assets/js/admin.js') || is_file(ROOT_PATH . '/assets/js/admin.min.js')): ?>
    <script src="<?= e(asset('js/admin.js')) ?>" defer></script>
<?php endif; ?>
<?php foreach ($extraJs as $jsFile): ?>
    <script src="<?= e(asset((string) $jsFile)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>