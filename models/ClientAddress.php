<?php

require_once __DIR__ . '/BaseModel.php';

class ClientAddress extends BaseModel {
    public function __construct() {
        parent::__construct('client_address');
    }

    /**
     * Deletes all address associations for a given client.
     */
    public function deleteByClientId(int $clientId): bool {
        return parent::delete(['client_id' => $clientId]);
    }

    /**
     * Retrieves all address IDs associated with a specific client.
     */
    public function getAddressIdsByClientId(int $clientId): array {
        try {
            $stmt = $this->pdo->prepare("SELECT address_id FROM client_address WHERE client_id = :client_id");
            $stmt->execute(['client_id' => $clientId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }
}
