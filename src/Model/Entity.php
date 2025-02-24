<?php

namespace AlisonAdamus\ActiveRecordProject\Model;

use AlisonAdamus\ActiveRecordProject\Util\Database;
use PDO;
use ReflectionClass;

abstract class Entity
{
    protected ?int $id = null;
    protected ?string $created_at = null;
    protected ?string $updated_at = null;
    private static ?PDO $pdo = null;

    public function __construct()
    {
        if (self::$pdo === null) {
            self::$pdo = Database::getConnection();
        }
    }
    abstract protected static function getTableName(): string;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function save(): bool
    {
        $table = static::getTableName();
        $reflection = new ReflectionClass($this);

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if ($property->getName() !== 'id' && !$property->isStatic()) {
                $properties[$property->getName()] = $property->getValue($this);

                if ($property->getName() === 'created_at' && empty($properties[$property->getName()])) {
                    $properties[$property->getName()] = date('Y-m-d H:i:s');
                }

                if ($property->getName() === 'updated_at') {
                    $properties[$property->getName()] = date('Y-m-d H:i:s');
                }
            }
        }

        if($this->id) {
            $setClause = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($properties)));
            $sql = "UPDATE $table SET $setClause WHERE id = :id";

            $stmt = self::$pdo->prepare($sql);
            $properties['id'] = $this->id;
            return $stmt->execute($properties);
        }else{
            $columns = implode(', ', array_keys($properties));
            $placeholders = implode(', ', array_map(fn($col) => ":$col", array_keys($properties)));
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

            $stmt = self::$pdo->prepare($sql);
            $success = $stmt->execute($properties);
            if ($success) {
                $this->id = (int) self::$pdo->lastInsertId();
            }
            return $success;
        }
    }
    public static function findAll(): array
    {
        $table = static::getTableName();
        $statement = self::$pdo->query("SELECT * FROM $table ORDER BY id DESC");
        return $statement->fetchAll(PDO::FETCH_CLASS, static::class);
    }

    public static function findById(int $id): ?static
    {
        $table = static::getTableName();
        $statement = self::$pdo->prepare("SELECT * FROM $table WHERE id = :id");
        $statement->execute([':id' => $id]);
        $statement->setFetchMode(PDO::FETCH_CLASS, static::class);
        return $statement->fetch() ?: null;
    }

    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }
        $table = static::getTableName();
        $stmt = self::$pdo->prepare("DELETE FROM $table WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = Database::getConnection();
        }
        return self::$pdo;
    }

    public static function deleteAll(): bool
    {
        $pdo = self::getPdo();
        $table = static::getTableName();
        $stmt = self::$pdo->prepare("DELETE FROM $table");
        return $stmt->execute();
    }
}