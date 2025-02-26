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
}
