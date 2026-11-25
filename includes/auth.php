<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

const ADMIN_MAX_FAILED_LOGINS = 5;
const ADMIN_LOCKOUT_SECONDS   = 900; // 15 minutes

/** Fingerprint of the browser, to make stolen session IDs less useful. */
function auth_fingerprint(): string
{
    return hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
}

/**
 * Log an admin in. Regenerates the session ID (prevents session fixation).
 */
function admin_login(int $adminId, string $username): void
{
    session_regenerate_id(true);
    csrf_rotate();

    $_SESSION['admin'] = [
        'id'       => $adminId,
        'username' => $username,
        'fp'       => auth_fingerprint(),
        'login_at' => time(),
        'last'     => time(),
    ];
}

/** Log the admin out, keeping the customer cart intact. */
function admin_logout(): void
{
    unset($_SESSION['admin']);
    session_regenerate_id(true);
    csrf_rotate();
}

/** True if a valid, non-expired admin session exists. */
function is_admin_logged_in(): bool
{
    $admin = $_SESSION['admin'] ?? null;

    if (!is_array($admin) || !isset($admin['id'], $admin['fp'], $admin['last'])) {
        return false;
    }

    if (!hash_equals((string) $admin['fp'], auth_fingerprint())) {
        unset($_SESSION['admin']);
        return false;
    }

    if ((time() - (int) $admin['last']) > ADMIN_IDLE_TIMEOUT) {
        unset($_SESSION['admin']);
        return false;
    }

    $_SESSION['admin']['last'] = time(); // sliding idle timeout
    return true;
}

function admin_id(): ?int
{
    return is_admin_logged_in() ? (int) $_SESSION['admin']['id'] : null;
}

function admin_username(): string
{
    return is_admin_logged_in() ? (string) ($_SESSION['admin']['username'] ?? '') : '';
}

/** Guard for admin pages: redirects to the login page if not logged in. */
function require_admin(): void
{
    // Admin pages must never be cached (back button after logout, shared PCs).
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }

    if (!is_admin_logged_in()) {
        flash_set('error', 'Please log in to continue.');
        redirect(url('admin/login.php'));
    }
}

/** Guard for admin POST actions: must be logged in, POST, with a valid CSRF token. */
function require_admin_post(): void
{
    if (!is_admin_logged_in()) {
        abort_with(403, 'You must be logged in to do that.');
    }
    csrf_require();
}

/* ------------------------------------------------------------------
 * Login throttling (session based; add DB/IP-based limits in a later phase)
 * ------------------------------------------------------------------ */

function admin_login_is_locked(): bool
{
    $until = (int) ($_SESSION['_login_lock_until'] ?? 0);
    if ($until > time()) {
        return true;
    }
    if ($until !== 0) {
        unset($_SESSION['_login_lock_until'], $_SESSION['_login_fails']);
    }
    return false;
}

function admin_login_record_failure(): void
{
    $_SESSION['_login_fails'] = (int) ($_SESSION['_login_fails'] ?? 0) + 1;
    if ($_SESSION['_login_fails'] >= ADMIN_MAX_FAILED_LOGINS) {
        $_SESSION['_login_lock_until'] = time() + ADMIN_LOCKOUT_SECONDS;
    }
}

function admin_login_reset_failures(): void
{
    unset($_SESSION['_login_fails'], $_SESSION['_login_lock_until']);
}

/**
 * Check credentials against the (future, Phase 2) `admins` table:
 *   id, username, password_hash
 * Uses prepared statements and a constant-ish time path for unknown users.
 * Returns true and logs the admin in on success.
 */
function admin_attempt_login(string $username, string $password): bool
{
    if (admin_login_is_locked()) {
        return false;
    }

    $admin = db_select_one(
        'SELECT id, username, password_hash FROM admins WHERE username = :u LIMIT 1',
        ['u' => $username]
    );

    // Dummy hash so unknown usernames cost the same time as wrong passwords.
    $hash = $admin['password_hash'] ?? '$2y$10$usesomesillystringforsalt$.invalidinvalidinvalidinvalidinvalid';
    $ok   = check_password($password, (string) $hash) && $admin !== null;

    if (!$ok) {
        admin_login_record_failure();
        app_log('Failed admin login for "' . clean_text($username, 50) . '" from ' . client_ip());
        return false;
    }

    // Upgrade the stored hash if the algorithm/cost changed.
    if (password_needs_upgrade((string) $admin['password_hash'])) {
        db_run(
            'UPDATE admins SET password_hash = :h WHERE id = :id',
            ['h' => make_password_hash($password), 'id' => (int) $admin['id']]
        );
    }

    admin_login_reset_failures();
    admin_login((int) $admin['id'], (string) $admin['username']);
    return true;
}