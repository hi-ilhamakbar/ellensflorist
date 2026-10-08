<?php

require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';

if (!headers_sent()) {
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
}

$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
$path = parse_url($requestUri, PHP_URL_PATH);
$route = trim($path ? $path : '', '/');

// Use array() rather than short array syntax so this front controller also
// remains parseable if a hosting account is accidentally assigned an old PHP handler.
$routes = array(
    '' => 'home',
    'about' => 'about',
    'wedding' => 'wedding',
    'wedding-decoration' => 'wedding-decoration',
    'wedding-inquiry' => 'wedding-inquiry',
    'gallery' => 'gallery',
    'testimonials' => 'testimonials',
    'how-to-order' => 'how-to-order',
    'faq' => 'faq',
    'blog' => 'blog',
    'contact' => 'contact',
    'privacy-policy' => 'privacy-policy',
    'terms' => 'terms',
);

if (isset($routes[$route])) {
    require __DIR__ . '/pages/' . $routes[$route] . '.php';
    exit;
}

if (preg_match('#^blog/([a-z0-9-]+)$#', $route, $matches)) {
    $post_slug = $matches[1];
    require __DIR__ . '/pages/blog-post.php';
    exit;
}

http_response_code(404);
require __DIR__ . '/pages/404.php';
