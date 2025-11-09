<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class PostController extends Controller
{
    // ------------------- FEED PAGE -------------------
    public function index()
    {
        Session::start();
        $userId = Session::get('user_id');
        $username = Session::get('username');

        if (!$userId) {
            header("Location: /login");
            exit;
        }

        $posts = Post::getAllWithUser();
        $this->view('auth/dashboard.php', ['posts' => $posts, 'userId' => $userId, 'username' => $username]);
    }

    // ------------------- CREATE POST -------------------
    public function create()
    {
        Session::start();
        $userId = Session::get('user_id');
        if (!$userId) {
            header("Location: /login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');
            $imagePath = null;

            if (!empty($_FILES['image']['name'])) {
                $uploadDir = 'public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                }
            }

            Post::create($userId, $content, $imagePath);
            header("Location: /dashboard");
            exit;
        }
    }

    // ------------------- LIKE POST -------------------
    public function like()
    {
        Session::start();
        $userId = Session::get('user_id');
        if (!$userId) {
            http_response_code(403);
            echo 'not_logged_in';
            return;
        }

        $postId = (int)($_POST['post_id'] ?? 0);

        if ($postId) {
            Post::toggleLike($userId, $postId);
        }

        echo Post::countLikes($postId);
    }

    // ------------------- ADD COMMENT -------------------
    public function comment()
    {
        Session::start();
        $userId = Session::get('user_id');
        $username = Session::get('username');

        if (!$userId) {
            http_response_code(403);
            echo 'not_logged_in';
            return;
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($postId && $comment !== '') {
            Post::addComment($userId, $postId, $comment);
        }

        // return updated comments HTML
        $this->renderComments(Post::getComments($postId), $userId, $username, $postId);
    }

    // ------------------- DELETE COMMENT -------------------
    public function deleteComment()
    {
        Session::start();
        $userId = Session::get('user_id');
        $username = Session::get('username');

        if (!$userId) {
            http_response_code(403);
            echo 'not_logged_in';
            return;
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        $postId = (int)($_POST['post_id'] ?? 0);

        if ($commentId) {
            Post::deleteComment($commentId, $userId);
        }

        $this->renderComments(Post::getComments($postId), $userId, $username, $postId);
    }

    // ------------------- HELPER -------------------
    private function renderComments(array $comments, int $userId, ?string $username, int $postId): void
    {
        foreach ($comments as $c) {
            echo '<div class="flex justify-between bg-gray-100 p-2 rounded mt-1 text-sm">';
            echo '<span><b class="text-blue-600">' . htmlspecialchars($c['username']) . ':</b> ' . htmlspecialchars($c['comment']) . '</span>';
            if ($c['user_id'] == $userId) {
                echo '<button class="delete-comment text-red-500" data-comment-id="' . $c['id'] . '" data-post-id="' . $postId . '">✖</button>';
            }
            echo '</div>';
        }
    }
}
