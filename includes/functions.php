<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

/* ==================================================================
 * Output escaping (XSS protection)
 * ================================================================== */

/** Escape a value for HTML text and attribute contexts. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Encode a value for safe use inside inline <script> blocks. */
function e_js(mixed $value): string
{
    return json_encode(
        $value,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );
}

/* ==================================================================
 * Environment, URLs and assets
 * ================================================================== */

/** True when the request arrived over HTTPS. */
function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443) {
        return true;
    }
    // Only trust proxy headers if explicitly enabled in config.local.php.
    if (defined('TRUST_PROXY_HTTPS') && TRUST_PROXY_HTTPS
        && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
        return true;
    }
    return false;
}

/** Build an application URL path, e.g. url('pages/catalog.php'). */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * URL for a file inside /assets, with cache-busting version.
 * In production, uses style.min.css / main.min.js if that file exists.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');

    if (APP_ENV === 'production') {
        $min = preg_replace('/\.(css|js)$/', '.min.$1', $path);
        if ($min !== null && $min !== $path && is_file(ROOT_PATH . '/assets/' . $min)) {
            $path = $min;
        }
    }

    $full    = ROOT_PATH . '/assets/' . $path;
    $version = is_file($full) ? '?v=' . filemtime($full) : '';

    return url('assets/' . $path) . $version;
}

/** Name of the current script without extension (e.g. "catalog"). */
function current_page(): string
{
    return basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''), '.php');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

/** Client IP (REMOTE_ADDR only; forwarded headers are spoofable). */
function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/** Redirect and stop. Strips CR/LF to prevent header injection. */
function redirect(string $location, int $status = 302): never
{
    $location = str_replace(["\r", "\n"], '', $location);
    header('Location: ' . $location, true, $status);
    exit;
}

/**
 * Allow only local paths for "redirect back" targets (prevents open redirects).
 */
function safe_redirect_target(?string $target, string $default = ''): string
{
    $target = trim((string) $target);

    if ($target === ''
        || $target[0] !== '/'
        || str_starts_with($target, '//')
        || str_contains($target, '\\')
        || preg_match('/[\r\n]/', $target)) {
        return url($default);
    }
    return $target;
}

/** Send security headers (also set in .htaccess; identical values). */
function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
    header('Content-Type: text/html; charset=UTF-8'); // JSON endpoints override this later
}

/** Stop with a minimal, safe error page. */
function abort_with(int $code, string $message): never
{
    http_response_code($code);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Error ' . $code . '</title></head>'
       . '<body><h1>' . $code . '</h1><p>' . e($message) . '</p></body></html>';
    exit;
}

/** Send a JSON response and stop (for future AJAX actions). */
function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

/* ==================================================================
 * Input reading, sanitizing and validation
 * (Sanitizing never replaces escaping on output or prepared statements.)
 * ================================================================== */

/** Trim, remove control characters, and cap the length. */
function clean_text(?string $value, int $maxLength = 255, bool $multiline = false): string
{
    $value = (string) $value;
    $value = preg_replace($multiline ? '/[^\P{C}\n]+/u' : '/\p{C}+/u', '', $value) ?? '';
    $value = trim($value);
    return mb_substr($value, 0, $maxLength, 'UTF-8');
}

/** Read a string from $_POST (arrays and missing keys return ''). */
function post_text(string $key, int $maxLength = 255, bool $multiline = false): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? clean_text($value, $maxLength, $multiline) : '';
}

/** Read a string from $_GET (arrays and missing keys return ''). */
function get_text(string $key, int $maxLength = 255): string
{
    $value = $_GET[$key] ?? '';
    return is_string($value) ? clean_text($value, $maxLength) : '';
}

/** Read an integer from $_POST or return $default. */
function post_int(string $key, ?int $default = null): ?int
{
    $value = filter_var($_POST[$key] ?? null, FILTER_VALIDATE_INT);
    return $value === false ? $default : (int) $value;
}

/** Read an integer from $_GET or return $default. */
function get_int(string $key, ?int $default = null): ?int
{
    $value = filter_var($_GET[$key] ?? null, FILTER_VALIDATE_INT);
    return $value === false ? $default : (int) $value;
}

function is_valid_email(string $email): bool
{
    return strlen($email) <= 254 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/** Indian mobile number, optional +91 prefix. */
function is_valid_phone(string $phone): bool
{
    return (bool) preg_match('/^(?:\+?91[\s-]?)?[6-9]\d{9}$/', $phone);
}

/**
 * Basic password rules. Returns a list of error messages (empty = OK).
 *
 * @return string[]
 */
function password_errors(string $password): array
{
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (strlen($password) > 72) { // bcrypt limit
        $errors[] = 'Password must be at most 72 characters long.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password must contain at least one letter and one number.';
    }
    return $errors;
}

/* ==================================================================
 * Passwords
 * ================================================================== */

function make_password_hash(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function check_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function password_needs_upgrade(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}

/* ==================================================================
 * CSRF protection
 * ================================================================== */

/** Current CSRF token (created on first use). */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/** Hidden input to place inside every POST form. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token): bool
{
    $known = $_SESSION['_csrf'] ?? '';
    return is_string($known) && $known !== '' && is_string($token) && hash_equals($known, $token);
}

/** Token from POST body or X-CSRF-Token header (for AJAX). */
function csrf_request_token(): ?string
{
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
    return is_string($token) ? $token : null;
}

/** Call at the top of every state-changing action. Stops on failure. */
function csrf_require(): void
{
    if (!is_post() || !csrf_verify(csrf_request_token())) {
        abort_with(403, 'Invalid or expired request. Please go back, refresh the page and try again.');
    }
}

/** Create a fresh token (after login/logout). */
function csrf_rotate(): void
{
    unset($_SESSION['_csrf']);
}

/* ==================================================================
 * Flash messages and old form input
 * ================================================================== */

function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/** @return array<int, array{type:string,message:string}> */
function flash_get(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return is_array($messages) ? $messages : [];
}

/** Print and clear flash messages (all values escaped). */
function render_flash(): void
{
    $allowed = ['success', 'error', 'info', 'warning'];
    foreach (flash_get() as $flash) {
        $type = in_array($flash['type'] ?? '', $allowed, true) ? $flash['type'] : 'info';
        echo '<div class="flash flash-' . e($type) . '" role="alert">' . e($flash['message'] ?? '') . '</div>';
    }
}

/** Remember submitted values (never store passwords). */
function remember_old_input(array $data): void
{
    unset($data['password'], $data['new_password'], $data['confirm_password'], $data['csrf_token']);
    $_SESSION['_old'] = $data;
}

/** Get an old input value once, already escaped. */
function old(string $key, string $default = ''): string
{
    $value = $_SESSION['_old'][$key] ?? $default;
    return e(is_scalar($value) ? $value : $default);
}

function clear_old_input(): void
{
    unset($_SESSION['_old']);
}

/* ==================================================================
 * Misc
 * ================================================================== */

/** Format a price in rupees: 450 -> ₹450, 450.5 -> ₹450.50 */
function format_price(float|int|string $amount): string
{
    $amount   = (float) $amount;
    $decimals = floor($amount) === $amount ? 0 : 2;
    return '₹' . number_format($amount, $decimals);
}

/** Write a line to logs/app.log (silently ignored if not writable). */
function app_log(string $message): void
{
    if (!is_dir(LOG_PATH) || !is_writable(LOG_PATH)) {
        return;
    }
    $line = '[' . date('Y-m-d H:i:s') . '] ' . str_replace(["\r", "\n"], ' ', $message) . PHP_EOL;
    @file_put_contents(LOG_PATH . '/app.log', $line, FILE_APPEND | LOCK_EX);
}