<?php

require_once __DIR__ . '/../core/DB.php';

/**
 * BaseModel provides generic database operations for all models.
 * It serves as the parent class for specific entity models.
 */
class BaseModel {
    protected PDO $pdo;
    protected string $table;

    /**
     * Initializes the database connection and sets the table name.
     *
     * @param string $table The name of the database table associated with the model.
     */
    public function __construct(string $table) {
        $this->pdo = DB::getConnection();
        $this->table = $table;
    }

    /**
     * Retrieves a single record based on specified conditions.
     *
     * @param array $conditions Key-value pairs for filtering the query.
     * @return array|null The retrieved record as an associative array, or null if not found.
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
     *
     * @param array $data An associative array of column-value pairs to insert.
     * @return object|null Returns an object containing the inserted record ID, or null on failure.
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
     *
     * @param array $conditions Key-value pairs specifying the records to delete.
     * @return bool Returns true if the operation was successful, false otherwise.
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
     *
     * @param array $data An associative array containing the updated data.
     *                   The 'id' key is required to specify which record to update.
     * @return bool Returns true if the update was successful, false otherwise.
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
