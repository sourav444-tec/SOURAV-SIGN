<?php
declare(strict_types=1);

// Block direct access (config.php defines ROOT_PATH first).
if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit('Forbidden');
}

// Optional local override (gitignored). Put real credentials THERE, never in Git.
$dbLocal = __DIR__ . '/database.local.php';
if (is_file($dbLocal)) {
    require $dbLocal;
}

// XAMPP defaults (safe for local development only).
defined('DB_HOST')    || define('DB_HOST', '127.0.0.1');
defined('DB_PORT')    || define('DB_PORT', 3306);
defined('DB_NAME')    || define('DB_NAME', 'souravs_stamp');
defined('DB_USER')    || define('DB_USER', 'root');
defined('DB_PASS')    || define('DB_PASS', '');
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a shared PDO connection (created on first use).
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST,
        (int) DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
            PDO::ATTR_PERSISTENT         => false,
        ]);
    } catch (PDOException $e) {
        // Log details privately; never show them to visitors.
        error_log('[DB] Connection failed: ' . $e->getMessage());
        throw new RuntimeException('Database connection failed.');
    }

    return $pdo;
}

/**
 * Run a prepared statement and return all rows.
 *
 * @param array<int|string, mixed> $params
 * @return array<int, array<string, mixed>>
 */
function db_select_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Run a prepared statement and return one row (or null).
 *
 * @param array<int|string, mixed> $params
 * @return array<string, mixed>|null
 */
function db_select_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/**
 * Run INSERT/UPDATE/DELETE with a prepared statement. Returns affected rows.
 *
 * @param array<int|string, mixed> $params
 */
function db_run(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}