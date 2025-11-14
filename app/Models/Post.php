<?php
namespace App\Models;

use PDO;

class Post
{
    // ------------------- DB CONNECTION -------------------
    private static function connect(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: 'database1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        return new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    // ------------------- CREATE POST -------------------
    public static function create(int $userId, string $content, ?string $image = null): bool
    {
        $stmt = self::connect()->prepare("
            INSERT INTO posts (user_id, content, image)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$userId, $content, $image]);
    }

    // ------------------- FEED POSTS -------------------
    public static function getAllWithUser(): array
{
    $stmt = self::connect()->query("
        SELECT 
            posts.*,
            users.name AS username,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC
    ");
    return $stmt->fetchAll();
}

public static function getByUser(int $userId): array
{
    $stmt = self::connect()->prepare("
        SELECT 
            posts.*,
            users.name AS username,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.user_id = ?
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}


    public static function getPostsByUser(int $userId): array
    {
        return self::getByUser($userId);
    }

    // ------------------- DELETE POST -------------------
    public static function delete(int $postId, int $userId): bool
    {
        $stmt = self::connect()->prepare("
            DELETE FROM posts
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$postId, $userId]);
    }

    // ------------------- LIKE / UNLIKE -------------------
    public static function toggleLike(int $userId, int $postId): void
    {
        $pdo = self::connect();

        $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$userId, $postId]);
        $found = $stmt->fetch();

        if ($found) {
            $pdo->prepare("DELETE FROM likes WHERE id = ?")->execute([$found['id']]);
        } else {
            $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)")->execute([$userId, $postId]);
        }
    }

    public static function countLikes(int $postId): int
    {
        $stmt = self::connect()->prepare("SELECT COUNT(*) AS cnt FROM likes WHERE post_id = ?");
        $stmt->execute([$postId]);
        return (int)$stmt->fetch()['cnt'];
    }

    // ------------------- COMMENTS -------------------
    public static function addComment(int $userId, int $postId, string $comment): void
    {
        $stmt = self::connect()->prepare("
            INSERT INTO comments (user_id, post_id, comment)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $postId, $comment]);
    }

   public static function getComments(int $postId): array
{
    $stmt = self::connect()->prepare("
        SELECT 
            comments.*,
            users.name AS username
        FROM comments
        JOIN users ON users.id = comments.user_id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at ASC
    ");
    $stmt->execute([$postId]);

    return $stmt->fetchAll();
}
    public static function deleteComment(int $commentId, int $userId): bool
    {
        $stmt = self::connect()->prepare("
            DELETE FROM comments
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$commentId, $userId]);
    }
}
