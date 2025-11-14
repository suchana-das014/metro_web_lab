<?php

namespace App\Controllers;

use App\Models\Database;
use App\Models\User;

class EditProfileController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $user = User::findById($_SESSION['user_id']);

        require __DIR__ . '/../Views/auth/edit_profile.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $username = trim($_POST['username']);
        $bio      = trim($_POST['bio']);

        // Upload image
        $profilePicture = null;

        if (!empty($_FILES['profile_picture']['name'])) {
            $targetDir = "uploads/profile/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . "_" . basename($_FILES['profile_picture']['name']);
            $filePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
                $profilePicture = $filePath;
            }
        }

        // Update DB
        $db = Database::getConnection();

        if ($profilePicture) {
            $sql = "UPDATE users SET username = ?, bio = ?, profile_picture = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username, $bio, $profilePicture, $userId]);
        } else {
            $sql = "UPDATE users SET username = ?, bio = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$username, $bio, $userId]);
        }

        header("Location: /my-profile");
        exit;
    }
}
