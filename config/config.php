<?php
declare(strict_types=1);

// Load only once.
if (defined('SS_BOOTSTRAPPED')) {
    return;
}
define('SS_BOOTSTRAPPED', true);

// Refuse direct browser access to this file.
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

/* ------------------------------------------------------------------
 * Filesystem paths (never printed to visitors)
 * ------------------------------------------------------------------ */
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');

/* ------------------------------------------------------------------
 * Optional local override (gitignored): config/config.local.php
 * May define: APP_ENV, BASE_URL, TRUST_PROXY_HTTPS, etc.
 * ------------------------------------------------------------------ */
$configLocal = __DIR__ . '/config.local.php';
if (is_file($configLocal)) {
    require $configLocal;
}

/* ------------------------------------------------------------------
 * Application constants
 * ------------------------------------------------------------------ */
defined('APP_ENV')        || define('APP_ENV', 'development');   // 'development' | 'production'
defined('APP_NAME')       || define('APP_NAME', "Sourav's Stamp");
defined('STORE_NAME')     || define('STORE_NAME', 'Sourav Sign Stamp Store');
defined('STORE_TAGLINE')  || define('STORE_TAGLINE', 'Your Sign • Our Quality');
// URL path of the project. XAMPP: '/souravs-stamp'. Production at domain root: ''.
defined('BASE_URL') || define('BASE_URL', '/SOURAV-SIGN');
defined('SESSION_NAME')   || define('SESSION_NAME', 'SSSESSID');
defined('ADMIN_IDLE_TIMEOUT') || define('ADMIN_IDLE_TIMEOUT', 1800); // seconds (30 min)

/* ------------------------------------------------------------------
 * Error handling
 * ------------------------------------------------------------------ */
error_reporting(E_ALL);
ini_set('log_errors', '1');

if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

if (is_dir(LOG_PATH) && is_writable(LOG_PATH)) {
    ini_set('error_log', LOG_PATH . '/php-error.log');
}

set_exception_handler(static function (Throwable $e): void {
    error_log('[UNCAUGHT] ' . get_class($e) . ': ' . $e->getMessage()
        . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    if (APP_ENV !== 'production') {
        echo '<pre style="white-space:pre-wrap">' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Error</title></head>'
           . '<body><h1>Something went wrong</h1><p>Please try again later.</p></body></html>';
    }
});

/* ------------------------------------------------------------------
 * Locale
 * ------------------------------------------------------------------ */
date_default_timezone_set('Asia/Kolkata');
mb_internal_encoding('UTF-8');

/* ------------------------------------------------------------------
 * Load helpers (order matters: functions first, session needs is_https())
 * ------------------------------------------------------------------ */
require_once INCLUDES_PATH . '/functions.php';

send_security_headers();

/* ------------------------------------------------------------------
 * Secure session (must happen before ANY output)
 * ------------------------------------------------------------------ */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.gc_maxlifetime', (string) ADMIN_IDLE_TIMEOUT);

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,                                   // until browser closes
        'path'     => (BASE_URL === '' ? '/' : rtrim(BASE_URL, '/') . '/'),
        'domain'   => '',
        'secure'   => is_https(),                          // true automatically on HTTPS
        'httponly' => true,                                // not readable by JavaScript
        'samesite' => 'Lax',                               // CSRF defence in depth
    ]);

    session_start();
}

/* ------------------------------------------------------------------
 * Remaining includes
 * ------------------------------------------------------------------ */
require_once CONFIG_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/cart-functions.php';

