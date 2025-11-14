<?php
namespace App\Models;

use PDO;

class User {

    private static function connect(): PDO {
        return new PDO(
            "mysql:host=127.0.0.1;dbname=db1;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public static function findById($id): ?array {
        $pdo = self::connect();

        $stmt = $pdo->prepare("
            SELECT id, name, username, email, bio, profile_picture 
            FROM users WHERE id = ?
        ");
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $pdo = self::connect();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        $row = $stmt->fetch();
        return $row ?: null;
    }
    public static function updateProfile($id, $name, $bio, $profile_picture = null)
{
    $pdo = self::connect();

    if ($profile_picture) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, bio = ?, profile_picture = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $bio, $profile_picture, $id]);
    }

    $stmt = $pdo->prepare("
        UPDATE users 
        SET name = ?, bio = ?
        WHERE id = ?
    ");

    return $stmt->execute([$name, $bio, $id]);
}


    public static function create(string $name, string $email, string $password): int {
        $pdo = self::connect();

        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password) 
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$name, $email, $password]);
        return (int)$pdo->lastInsertId();
    }
}
