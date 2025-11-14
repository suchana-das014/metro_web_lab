<?php
namespace App\Controllers;

use App\Core\Session;
use App\Models\User;
use App\Models\Post;

class ProfileController {

    public function index() {
        Session::start();

        // ------- FIX #1: If session has wrong user_id, reset -------
        $userId = Session::get('user_id');

        if (!$userId || !User::findById($userId)) {
            Session::destroy();
            header("Location: /login");
            exit;
        }

        // ------- Load user -------
        $user = User::findById($userId);

        // ------- Load user's posts -------
        $posts = Post::getByUser($userId);

        require __DIR__ . '/../Views/auth/profile.php';

    }


    public function edit() {
        Session::start();

        $userId = Session::get('user_id');

        if (!$userId || !User::findById($userId)) {
            Session::destroy();
            header("Location: /login");
            exit;
        }

        $user = User::findById($userId);

        require __DIR__ . '/../Views/auth/edit_profile.php';

    }


    public function update() {
        Session::start();

        $userId = Session::get('user_id');

        if (!$userId || !User::findById($userId)) {
            Session::destroy();
            header("Location: /login");
            exit;
        }

        $name = $_POST['name'] ?? '';
        $bio  = $_POST['bio'] ?? '';

        $profilePicture = null;

        if (!empty($_FILES['profile_picture']['name'])) {
            $file = $_FILES['profile_picture'];
            $target = 'uploads/profile/' . time() . "_" . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $target);
            $profilePicture = $target;
        }

        $pdo = new \PDO(
            "mysql:host=127.0.0.1;dbname=db1;charset=utf8mb4",
            "root",
            ""
        );

        if ($profilePicture) {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET name=?, bio=?, profile_picture=? 
                WHERE id=?
            ");
            $stmt->execute([$name, $bio, $profilePicture, $userId]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET name=?, bio=?
                WHERE id=?
            ");
            $stmt->execute([$name, $bio, $userId]);
        }

        header("Location: /my-profile");
        exit;
    }
}
