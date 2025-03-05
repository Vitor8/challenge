<?php

require_once __DIR__ . '/BaseModel.php';

class Address extends BaseModel {
    public function __construct() {
        parent::__construct('addresses');
    }

    /**
     * Deletes all addresses related to a specific client.
     */
    public function deleteByClientId(int $clientId): bool {
        try {
            $sql = "DELETE FROM addresses WHERE id IN (
                        SELECT address_id FROM client_address WHERE client_id = :client_id
                    )";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['client_id' => $clientId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Retrieves addresses by an array of address IDs.
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
            return [];
        }
    }
}
