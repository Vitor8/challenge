<?php
require_once __DIR__ . '/../database/database.php';

class BaseModel {
    protected $pdo;
    protected $table;

    public function __construct($table) {
        global $pdo;
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function get($conditions = []) {
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
