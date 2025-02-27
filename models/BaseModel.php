<?php
require_once __DIR__ . '/../core/DB.php';

class BaseModel {
    protected $pdo;
    protected $table;

    public function __construct($table) {
        $this->pdo = DB::getConnection();
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
    
        if ($stmt->execute($data)) {
            return (object) ['id' => $this->pdo->lastInsertId()];
        }
        
        return false;
    }

    public function delete($conditions) {
        $whereClauses = [];
        foreach ($conditions as $key => $value) {
            $whereClauses[] = "$key = :$key";
        }
        $sql = "DELETE FROM {$this->table} WHERE " . implode(" AND ", $whereClauses);
    
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($conditions);
    }
    
}
