<?php
declare(strict_types=1);

// Load vendor
require __DIR__ . '/../vendor/autoload.php';

// Load .env FIRST
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

// Start session AFTER env
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    session_start();
}

use App\Core\Router;
use App\Core\Session;

Session::start();

$router = new Router();

// ----------------------
// AUTH ROUTES
// ----------------------
$router->get('/', 'AuthController@showLogin');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/logout', 'AuthController@logout');

// Private profile (your own)
$router->get('/my-profile', 'ProfileController@index');


// Public profile — WILL WORK once Router is updated
$router->get('/user/{username}', 'AuthController@publicProfile');

// ----------------------
// POSTS & FEED
// ----------------------
$router->get('/dashboard', 'PostController@index');
$router->post('/create_post', 'PostController@create');
$router->post('/like', 'PostController@like');
$router->post('/comment', 'PostController@comment');
$router->post('/delete_comment', 'PostController@deleteComment');
$router->get('/my-profile', 'ProfileController@index');
$router->get('/edit-profile', 'ProfileController@edit');
$router->post('/update-profile', 'ProfileController@update');
$router->post('/edit-profile/update', 'EditProfileController@update');

// ----------------------
// DISPATCH
// ----------------------
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
