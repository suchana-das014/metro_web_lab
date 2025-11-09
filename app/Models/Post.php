<?php
namespace App\Models;

use PDO;

class Post
{
    // ✅ Central database connection
    private static function connect(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'database1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }

    // ✅ Create new post
    public static function create(int $userId, string $content, ?string $image = null): bool
    {
        $stmt = self::connect()->prepare('
            INSERT INTO posts (user_id, content, image) 
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$userId, $content, $image]);
    }

    // ✅ Fetch all posts with user info
    public static function getAllWithUser(): array
    {
        $stmt = self::connect()->query('
            SELECT posts.*, users.name AS username
            FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.created_at DESC
        ');
        return $stmt->fetchAll();
    }

    // ✅ Fetch posts by one user
    public static function getByUser(int $userId): array
    {
        $stmt = self::connect()->prepare('
            SELECT posts.*, users.name AS username
            FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE user_id = ?
            ORDER BY posts.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // ✅ Delete post (owned by user)
    public static function delete(int $postId, int $userId): bool
    {
        $stmt = self::connect()->prepare('
            DELETE FROM posts 
            WHERE id = ? AND user_id = ?
        ');
        return $stmt->execute([$postId, $userId]);
    }

    // ✅ Like/unlike toggle
    public static function toggleLike(int $userId, int $postId): void
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare('SELECT id FROM likes WHERE user_id = ? AND post_id = ?');
        $stmt->execute([$userId, $postId]);
        $like = $stmt->fetch();

        if ($like) {
            $pdo->prepare('DELETE FROM likes WHERE id = ?')->execute([$like['id']]);
        } else {
            $pdo->prepare('INSERT INTO likes (user_id, post_id) VALUES (?, ?)')->execute([$userId, $postId]);
        }
    }

    // ✅ Count total likes for a post
    public static function countLikes(int $postId): int
    {
        $stmt = self::connect()->prepare('SELECT COUNT(*) as cnt FROM likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        $row = $stmt->fetch();
        return (int)$row['cnt'];
    }

    // ✅ Add comment
    public static function addComment(int $userId, int $postId, string $comment): void
    {
        $stmt = self::connect()->prepare('INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $postId, $comment]);
    }

    // ✅ Get all comments for a post
    public static function getComments(int $postId): array
    {
        $stmt = self::connect()->prepare('
            SELECT comments.*, users.name AS username
            FROM comments
            JOIN users ON users.id = comments.user_id
            WHERE post_id = ?
            ORDER BY comments.created_at ASC
        ');
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    // ✅ Delete comment safely
    public static function deleteComment(int $commentId, int $userId): bool
    {
        $pdo = self::connect();
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
        return $stmt->execute([$commentId, $userId]);
    }
}
