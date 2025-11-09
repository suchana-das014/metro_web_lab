<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class DashboardController extends Controller {
    public function index(): void
    {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $posts = Post::getAllWithUser();

        $this->view('dashboard.php', [
            'user' => $user,
            'posts' => $posts
        ]);
    }
}
