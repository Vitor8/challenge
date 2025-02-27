<?php
require_once __DIR__ . '/BaseModel.php';

class ClientAddress extends BaseModel {
    public function __construct() {
        parent::__construct('client_address');
    }

    public function deleteByClientId($clientId) {
        $sql = "DELETE FROM client_address WHERE client_id = :client_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
    }
}
