<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Mailer;
use App\Models\User;

class AuthController extends Controller
{
    // -----------------------
    // SHOW LOGIN PAGE
    // -----------------------
    public function showLogin()
    {
        $this->view('auth/login.php');
    }

    // -----------------------
    // SHOW REGISTER PAGE
    // -----------------------
    public function showRegister()
    {
        $this->view('auth/register.php');
    }

    // -----------------------
    // HANDLE REGISTRATION
    // -----------------------
    public function register()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            return;
        }

        if (strlen($password) < 6) {
            echo "Password must be at least 6 characters.";
            return;
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        User::create($name, $email, $hashed);

        Mailer::send(
            $email,
            'Welcome to AuthBoard',
            "Hello $name,\n\nThanks for registering at AuthBoard."
        );

        header('Location: /login');
        exit;
    }

    // -----------------------
    // PROFILE PAGE (MY PROFILE)
    // -----------------------
    public function profile()
    {
        Session::start();
var_dump($_SESSION);
exit;


        $userId = Session::get('user_id');

        if (!$userId) {
            header("Location: /login");
            exit;
        }

        // Load user from DB (DATABASE MUST BE db1)
        $pdo = new \PDO(
            "mysql:host=" . (getenv('DB_HOST') ?: '127.0.0.1') .
            ";dbname=" . (getenv('DB_NAME') ?: 'db1') .
            ";charset=utf8mb4",
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASS') ?: ''
        );

        // Fetch user info
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "User not found";
            exit;
        }

        // Fetch user's posts
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll();

        $this->view('auth/profile.php', ['user' => $user, 'posts' => $posts]);
    }

    // -----------------------
    // HANDLE LOGIN
    // -----------------------
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {

            Session::start();
            Session::set('user_id', $user['id']);
            Session::set('username', $user['name']);
            Session::set('user_email', $user['email']);

            header('Location: /dashboard');
            exit;
        }

        echo "Invalid credentials.";
    }

    // -----------------------
    // LOGOUT
    // -----------------------
    public function logout(): void
    {
        Session::destroy();
        header('Location: /login');
        exit;
    }
}
