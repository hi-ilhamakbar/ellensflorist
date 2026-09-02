<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
date_default_timezone_set('Asia/Singapore');

function env(string $name, ?string $default = null): ?string {
    static $values;
    if ($values === null) {
        $values = $_ENV;
        $file = ROOT_PATH . '/.env';
        if (is_readable($file)) foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (!str_starts_with(trim($line), '#') && str_contains($line, '=')) { [$k, $v] = explode('=', $line, 2); $values[trim($k)] = trim($v); }
        }
    }
    return $values[$name] ?? getenv($name) ?: $default;
}
function db(): PDO {
    static $pdo;
    if (!$pdo) $pdo = new PDO('mysql:host='.env('DB_HOST','localhost').';dbname='.env('DB_NAME').';charset=utf8mb4', env('DB_USER'), env('DB_PASS'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
    return $pdo;
}
function e(?string $text): string { return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return rtrim(env('APP_URL','https://ellensflorist.com'), '/') . ($path ? '/' . ltrim($path, '/') : ''); }
function asset_url(string $path): string {
    $file = ROOT_PATH . '/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '';
    return $path . ($version !== '' ? '?v=' . rawurlencode($version) : '');
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Your session has expired. Please return and try again.'); } }
function start_secure_session(): void { session_set_cookie_params(['httponly'=>true,'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),'samesite'=>'Lax']); session_start(); }
function captcha(): array { $a=random_int(1,9); $b=random_int(1,9); $subtract=(bool)random_int(0,1); if($subtract && $b>$a) [$a,$b]=[$b,$a]; $_SESSION['captcha_answer']=$subtract?$a-$b:$a+$b; return [$a, $subtract ? '−' : '+', $b]; }
function rate_limit(string $action, int $limit=5, int $seconds=3600): bool { $key='rate_'.$action.'_'.hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''); $row=$_SESSION[$key] ?? ['count'=>0,'at'=>time()]; if(time()-$row['at']>$seconds) $row=['count'=>0,'at'=>time()]; $row['count']++; $_SESSION[$key]=$row; return $row['count'] <= $limit; }
function published_content(string $type): array { try { $q=db()->prepare('SELECT * FROM content WHERE type=? AND status="published" ORDER BY published_at DESC, id DESC'); $q->execute([$type]); return $q->fetchAll(); } catch (Throwable $e) { return []; } }
function real_image_path(?string $path): string {
    // Keep previously published CMS entries working while the media library moves to the new wedding collection.
    $replacements = [
        '/assets/images/hero-wedding-florals.png' => '/assets/images/mega-will-ceremony.webp',
        '/assets/images/bridal-bouquet.png' => '/assets/images/jisoo-sabrina-ceremony.webp',
        '/assets/images/ceremony-arch.png' => '/assets/images/mega-will-reception.webp',
        '/assets/images/reception-tablescape.png' => '/assets/images/mega-will-table.webp',
        '/assets/images/temporary-wedding-reception.png' => '/assets/images/jisoo-sabrina-reception.webp',
        '/assets/images/cassy-bouquet.webp' => '/assets/images/jisoo-sabrina-ceremony.webp',
        '/assets/images/cassy-ceremony.webp' => '/assets/images/mega-will-ceremony.webp',
        '/assets/images/cassy-details.webp' => '/assets/images/jisoo-sabrina-reception.webp',
        '/assets/images/cassy-reception.webp' => '/assets/images/mega-will-reception.webp',
        '/assets/images/cassy-table.webp' => '/assets/images/mega-will-table.webp',
        '/assets/images/chloe-ceremony.webp' => '/assets/images/mega-will-table.webp',
        '/assets/images/chloe-details.webp' => '/assets/images/jisoo-sabrina-ceremony.webp',
        '/assets/images/chloe-reception.webp' => '/assets/images/mega-will-dance.webp',
        '/assets/images/claire-ceremony.webp' => '/assets/images/mega-will-ceremony.webp',
        '/assets/images/claire-details.webp' => '/assets/images/jisoo-sabrina-reception.webp',
        '/assets/images/claire-reception.webp' => '/assets/images/mega-will-details.webp',
    ];

    return asset_url($replacements[$path] ?? (string) $path);
}
start_secure_session();
