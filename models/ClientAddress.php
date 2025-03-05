<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * ClientAddress Model - Handles client-address relationships in the database.
 */
class ClientAddress extends BaseModel {
    
    /**
     * Initializes the ClientAddress model with the associated database table.
     */
    public function __construct() {
        parent::__construct('client_address');
    }

    /**
     * Deletes all address associations for a given client.
     *
     * @param int $clientId The ID of the client whose addresses should be removed.
     * @return bool Returns true if deletion was successful, false otherwise.
     */
    public function deleteByClientId(int $clientId): bool {
        try {
            return parent::delete(['client_id' => $clientId]);
        } catch (PDOException $e) {
            error_log("Error deleting client addresses for client ID $clientId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all address IDs associated with a specific client.
     *
     * @param int $clientId The ID of the client whose addresses should be retrieved.
     * @return array Returns an array of address IDs linked to the given client.
     */
    public function getAddressIdsByClientId(int $clientId): array {
        try {
            $stmt = $this->pdo->prepare("SELECT address_id FROM client_address WHERE client_id = :client_id");
            $stmt->execute(['client_id' => $clientId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (PDOException $e) {
            error_log("Error retrieving addresses for client ID $clientId: " . $e->getMessage());
            return [];
        }
    }
}
