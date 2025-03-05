<?php

require_once __DIR__ . '/../core/DB.php';

/**
 * BaseModel provides common database operations for all models.
 */
class BaseModel {
    protected PDO $pdo;
    protected string $table;

    /**
     * Constructor to initialize database connection and table name.
     */
    public function __construct(string $table) {
        $this->pdo = DB::getConnection();
        $this->table = $table;
    }

    /**
     * Retrieves a single record based on given conditions.
     */
    public function get(array $conditions = []): ?array {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $whereClauses = array_map(fn($key) => "$key = :$key", array_keys($conditions));
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($conditions);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Inserts a new record into the table.
     */
    public function create(array $data): ?object {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data) ? (object) ['id' => $this->pdo->lastInsertId()] : null;
    }

    /**
     * Deletes records matching the given conditions.
     */
    public function delete(array $conditions): bool {
        if (empty($conditions)) {
            return false;
        }

        $whereClauses = array_map(fn($key) => "$key = :$key", array_keys($conditions));
        $sql = "DELETE FROM {$this->table} WHERE " . implode(" AND ", $whereClauses);

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($conditions);
    }

    /**
     * Updates an existing record based on the provided data.
     */
    public function edit(array $data): bool {
        if (!isset($data['id'])) {
            return false;
        }

        $columns = array_filter(array_keys($data), fn($key) => $key !== 'id');
        $setClauses = array_map(fn($key) => "$key = :$key", $columns);

        $sql = "UPDATE {$this->table} SET " . implode(", ", $setClauses) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($data);
    }
}
