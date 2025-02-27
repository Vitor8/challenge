<?php
require_once __DIR__ . '/BaseModel.php';

class Address extends BaseModel {
    public function __construct() {
        parent::__construct('addresses');
    }

    public function deleteByClientId($clientId) {
        $sql = "DELETE FROM addresses WHERE id IN (SELECT address_id FROM client_address WHERE client_id = :client_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
    }
}
