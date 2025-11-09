<?php
declare(strict_types=1);

// ✅ Load environment first
require __DIR__ . '/../vendor/autoload.php';

// ✅ .env loader (must define before session)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1 => null]);
        if ($key && $val !== null) {
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

// ✅ Start session safely after environment loaded
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/'); // set path before starting session
    session_start();
}

use App\Core\Router;
use App\Core\Session;

// always ensure Session helper starts too
Session::start();

use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\DashboardController;

$router = new Router();

// ---- Auth Routes ----
$router->get('/', 'AuthController@showLogin');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/profile', 'AuthController@profile');


// ---- Feed & Post ----
$router->get('/dashboard', 'PostController@index');
$router->post('/create_post', 'PostController@create');
$router->post('/like', 'PostController@like');
$router->post('/comment', 'PostController@comment');
$router->post('/delete_comment', 'PostController@deleteComment');

// ---- Dispatch ----
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$router->dispatch($uri, $method);
