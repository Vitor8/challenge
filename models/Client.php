<?php
require_once __DIR__ . '/BaseModel.php';

class Client extends BaseModel {
    public function __construct() {
        parent::__construct('clients');
    }

    public function getAllWithAddresses($start, $limit) {
        global $pdo;

        $stmt = $pdo->prepare("
            SELECT c.id, c.name, DATE_FORMAT(c.birth, '%d/%m/%Y') as birth, c.cpf, c.rg, c.phone
            FROM clients c
            ORDER BY c.id DESC
            LIMIT :start, :limit
        ");
        $stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clients as &$client) {
            $stmt = $pdo->prepare("
                SELECT a.street, a.number, a.zip_code, a.city, a.state
                FROM addresses a
                INNER JOIN client_address ca ON ca.address_id = a.id
                WHERE ca.client_id = :client_id
            ");
            $stmt->execute(['client_id' => $client['id']]);
            $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formattedAddresses = [];
            foreach ($addresses as $address) {
                $formattedAddresses[] = "Endereço: {$address['street']}, {$address['number']}, {$address['zip_code']}, {$address['city']}, {$address['state']}";
            }

            $client['addresses'] = implode("; ", $formattedAddresses);
        }

        return $clients;
    }
    
    public function countClients() {
        global $pdo;
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM clients");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
