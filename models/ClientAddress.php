<?php
require_once __DIR__ . '/BaseModel.php';

class ClientAddress extends BaseModel {
    public function __construct() {
        parent::__construct('client_address');
    }

    public function deleteByClientId($clientId) {
        return parent::delete(['client_id' => $clientId]);
    }
    

    public function getAddressIdsByClientId($clientId) {
        $stmt = $this->pdo->prepare("SELECT address_id FROM client_address WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
