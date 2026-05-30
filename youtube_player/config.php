<?php
declare(strict_types=1);

// ==============================
// Cafe24 DB 설정
// ==============================
define('DB_HOST', 'localhost');
define('DB_NAME', 'rnjslee');
define('DB_USER', 'rnjslee');
define('DB_PASS', 'ajou2130++');
define('DB_CHARSET', 'utf8mb4');

// 관리자 로그인 비밀번호 - 반드시 변경하세요.
define('ADMIN_PASSWORD', '0911');

// 사이트 기본 URL - 예: https://도메인.com/youtube_player
// 비워두면 자동 추정합니다.
define('BASE_URL', 'https://rnjslee.mycafe24.com/youtube_player');

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function base_url(): string {
    if (BASE_URL !== '') return rtrim(BASE_URL, '/');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $dir = preg_replace('~/admin$~', '', $dir);
    return $scheme.'://'.$host.($dir === '' ? '' : $dir);
}

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function require_admin(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['yt_admin'])) {
        header('Location: login.php'); exit;
    }
}

function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400); exit('잘못된 요청입니다.');
    }
}

function youtube_id_from_url(string $url): string {
    $url = trim($url);
    if (preg_match('~^[A-Za-z0-9_-]{11}$~', $url)) return $url;
    $parts = parse_url($url);
    if (!$parts) return '';
    $host = strtolower($parts['host'] ?? '');
    $path = trim($parts['path'] ?? '', '/');
    parse_str($parts['query'] ?? '', $q);
    if (isset($q['v']) && preg_match('~^[A-Za-z0-9_-]{11}$~', $q['v'])) return $q['v'];
    if (str_contains($host, 'youtu.be')) {
        $seg = explode('/', $path)[0] ?? '';
        return preg_match('~^[A-Za-z0-9_-]{11}$~', $seg) ? $seg : '';
    }
    if (str_contains($host, 'youtube.com')) {
        $segs = explode('/', $path);
        foreach (['embed','shorts','live'] as $type) {
            $idx = array_search($type, $segs, true);
            if ($idx !== false && isset($segs[$idx+1]) && preg_match('~^[A-Za-z0-9_-]{11}$~', $segs[$idx+1])) return $segs[$idx+1];
        }
    }
    return '';
}

function get_setting(string $key, string $default=''): string {
    $stmt = db()->prepare('SELECT setting_value FROM yt_settings WHERE setting_key=?');
    $stmt->execute([$key]);
    $v = $stmt->fetchColumn();
    return $v === false ? $default : (string)$v;
}

function set_setting(string $key, string $value): void {
    $stmt = db()->prepare('INSERT INTO yt_settings(setting_key, setting_value) VALUES(?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}
