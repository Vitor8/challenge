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

    public function getAddressesByIds($addressIds) {
        if (empty($addressIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($addressIds), '?'));

        $stmt = $this->pdo->prepare("SELECT street, number, zip_code, city, district, state FROM addresses WHERE id IN ($placeholders)");
        $stmt->execute($addressIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
