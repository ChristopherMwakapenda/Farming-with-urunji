<?php
declare(strict_types=1);
/*
 * FARMING WITH URUNJI — INFINITYFREE CONFIGURATION
 *
 * Hosted MySQL database configuration.
 * Keep the remaining functions in this file unchanged.
 */
const DB_HOST = 'sql102.infinityfree.com';
const DB_NAME = 'if0_42903008_urunji';
const DB_USER = 'if0_42903008';
const DB_PASS = 'Amwaka1234';
const DB_PORT = 3306;
const DB_CHARSET = 'utf8mb4';
const SESSION_NAME = 'fwu_session_v1';
const SESSION_LIFETIME = 28800; // 8 hours
function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name(SESSION_NAME);
    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
    session_cache_limiter('nocache');
    $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
    $secure = in_array($https, ['on', '1'], true);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    if (
        str_contains(DB_NAME, 'YOUR_') ||
        str_contains(DB_HOST, 'YOUR_') ||
        str_contains(DB_PASS, 'PASTE_YOUR_')
    ) {
        throw new RuntimeException(
            'Database configuration is incomplete. Check config.php.'
        );
    }
    $dsn = 'mysql:host=' . DB_HOST
        . ';port=' . DB_PORT
        . ';dbname=' . DB_NAME
        . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    return $pdo;
}
function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
function redirect(string $url = 'index.php'): never
{
    header('Location: ' . $url);
    exit;
}
function flash(string $type, string $message): void
{
    $_SESSION['_flash'] = [
        'type' => $type,
        'message' => $message
    ];
}
function get_flash(): ?array
{
    $flash = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return is_array($flash) ? $flash : null;
}
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        redirect('index.php?page=login');
    }
    $now = time();
    if (
        isset($_SESSION['last_activity']) &&
        ($now - (int) $_SESSION['last_activity']) > SESSION_LIFETIME
    ) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        redirect('index.php?page=login');
    }
    $_SESSION['last_activity'] = $now;
}
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['_csrf'];
}
function verify_csrf(): void
{
    $sessionToken = (string) ($_SESSION['_csrf'] ?? '');
    $postedToken = (string) ($_POST['_csrf'] ?? '');
    if (
        $sessionToken === '' ||
        $postedToken === '' ||
        !hash_equals($sessionToken, $postedToken)
    ) {
        http_response_code(419);
        exit('Invalid security token. Please go back and try again.');
    }
}