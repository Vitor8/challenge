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

    public function delete($clientId) {
        global $pdo;
    
        try {
            $pdo->beginTransaction();
    
            $stmt = $pdo->prepare("DELETE FROM client_address WHERE client_id = :client_id");
            $stmt->execute(['client_id' => $clientId]);
            $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
            $stmt->execute(['id' => $clientId]);
    
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public function getClientDataById($filters) {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
        $stmt->execute(['id' => $filters['id']]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$client) {
            return null;
        }
    
        $stmt = $pdo->prepare("SELECT * FROM addresses 
                               INNER JOIN client_address ON addresses.id = client_address.address_id
                               WHERE client_address.client_id = :id");
        $stmt->execute(['id' => $filters['id']]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return [
            'name' => $client['name'],
            'birth' => date('d/m/Y', strtotime($client['birth'])),
            'cpf' => $client['cpf'],
            'rg' => $client['rg'],
            'phone' => $client['phone'],
            'addresses' => $addresses
        ];
    }
}
