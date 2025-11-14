<?php
namespace App\Models;

use PDO;

class Database
{
    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            self::$connection = new PDO(
                "mysql:host=127.0.0.1;dbname=db1;charset=utf8mb4",
                "root",
                ""
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }
}
