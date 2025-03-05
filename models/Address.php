<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * Address Model - Manages database operations related to addresses.
 */
class Address extends BaseModel {
    
    /**
     * Initializes the Address model with the associated database table.
     */
    public function __construct() {
        parent::__construct('addresses');
    }

    /**
     * Deletes all addresses associated with a specific client.
     *
     * @param int $clientId The ID of the client whose addresses should be deleted.
     * @return bool Returns true if the operation was successful, false otherwise.
     */
    public function deleteByClientId(int $clientId): bool {
        try {
            $sql = "DELETE FROM addresses WHERE id IN (
                        SELECT address_id FROM client_address WHERE client_id = :client_id
                    )";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['client_id' => $clientId]);
        } catch (PDOException $e) {
            error_log("Error deleting addresses for client ID $clientId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves multiple addresses based on an array of address IDs.
     *
     * @param array $addressIds An array of address IDs to retrieve.
     * @return array Returns an array of address records or an empty array if no records are found.
     */
    public function getAddressesByIds(array $addressIds): array {
        if (empty($addressIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($addressIds), '?'));

        try {
            $stmt = $this->pdo->prepare("SELECT street, number, zip_code, city, district, state 
                                         FROM addresses WHERE id IN ($placeholders)");
            $stmt->execute($addressIds);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error retrieving addresses: " . $e->getMessage());
            return [];
        }
    }
}
