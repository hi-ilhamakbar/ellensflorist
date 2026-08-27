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
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Your session has expired. Please return and try again.'); } }
function start_secure_session(): void { session_set_cookie_params(['httponly'=>true,'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),'samesite'=>'Lax']); session_start(); }
function captcha(): array { $a=random_int(1,9); $b=random_int(1,9); $subtract=(bool)random_int(0,1); if($subtract && $b>$a) [$a,$b]=[$b,$a]; $_SESSION['captcha_answer']=$subtract?$a-$b:$a+$b; return [$a, $subtract ? '−' : '+', $b]; }
function rate_limit(string $action, int $limit=5, int $seconds=3600): bool { $key='rate_'.$action.'_'.hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''); $row=$_SESSION[$key] ?? ['count'=>0,'at'=>time()]; if(time()-$row['at']>$seconds) $row=['count'=>0,'at'=>time()]; $row['count']++; $_SESSION[$key]=$row; return $row['count'] <= $limit; }
function published_content(string $type): array { try { $q=db()->prepare('SELECT * FROM content WHERE type=? AND status="published" ORDER BY published_at DESC, id DESC'); $q->execute([$type]); return $q->fetchAll(); } catch (Throwable $e) { return []; } }
start_secure_session();
