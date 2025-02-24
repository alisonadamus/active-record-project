<?php

namespace AlisonAdamus\ActiveRecordProject\Util;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    private static array $config = [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => 'active_record_project',
        'username' => 'postgres',
        'password' => '',
        'charset' => 'utf8'
    ];

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = self::$config;
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password'],[
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Кидати винятки при помилках
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,  // Автомапінг у класи
                    PDO::ATTR_EMULATE_PREPARES => false,  // Використовуємо справжні підготовлені запити
                    PDO::ATTR_PERSISTENT => false  // Не використовуємо постійні з'єднання
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function createTables(): void
    {
        $pdo = self::getConnection();

        $createArticlesTable = "
        CREATE TABLE IF NOT EXISTS articles (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            image VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP
        );
        ";
        $pdo->exec($createArticlesTable);

        $createCommentsTable = "
        CREATE TABLE IF NOT EXISTS comments (
            id SERIAL PRIMARY KEY,
            article_id INT NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
        );
        ";
        $pdo->exec($createCommentsTable);
    }
}