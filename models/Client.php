<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/ClientAddress.php';
require_once __DIR__ . '/Address.php';

class Client extends BaseModel {
    public function __construct() {
        parent::__construct('clients');
    }

    /**
     * Retrieves all clients along with their formatted addresses.
     */
    public function getAllWithAddresses(int $start, int $limit): array {
        $stmt = $this->pdo->prepare("
            SELECT c.id, c.name, DATE_FORMAT(c.birth, '%d/%m/%Y') as birth, c.cpf, c.rg, c.phone
            FROM clients c
            ORDER BY c.id DESC
            LIMIT :start, :limit
        ");

        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->attachFormattedAddresses($clients);
    }

    /**
     * Counts the total number of clients in the database.
     */
    public function countClients(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM clients");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Deletes a client along with its associated addresses.
     */
    public function deleteClient(int $clientId): bool {
        try {
            $this->pdo->beginTransaction();

            $clientAddressModel = new ClientAddress();
            $clientAddressModel->delete(['client_id' => $clientId]);
            parent::delete(['id' => $clientId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Retrieves client details by ID, including addresses.
     */
    public function getClientDataById(array $filters): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE id = :id");
        $stmt->execute(['id' => $filters['id']]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            return null;
        }

        $clientAddressModel = new ClientAddress();
        $addressModel = new Address();

        $addressIds = $clientAddressModel->getAddressIdsByClientId($filters['id']);
        $addresses = $addressModel->getAddressesByIds($addressIds);

        return [
            'name' => $client['name'],
            'birth' => date('d-m-Y', strtotime($client['birth'])),
            'cpf' => $client['cpf'],
            'rg' => $client['rg'],
            'phone' => $client['phone'],
            'addresses' => $addresses
        ];
    }

    /**
     * Updates client information.
     */
    public function edit(array $data): bool {
        return parent::edit($data);
    }

    /**
     * Attaches formatted addresses to the client data.
     */
    private function attachFormattedAddresses(array $clients): array {
        $clientAddressModel = new ClientAddress();
        $addressModel = new Address();

        foreach ($clients as &$client) {
            $addressIds = $clientAddressModel->getAddressIdsByClientId($client['id']);
            $addresses = $addressModel->getAddressesByIds($addressIds);

            $formattedAddresses = array_map(fn($address) =>
                "Endereço: {$address['street']}, {$address['number']}, {$address['zip_code']}, {$address['city']}, {$address['state']}",
                $addresses
            );

            $client['addresses'] = implode("; ", $formattedAddresses);
        }

        return $clients;
    }
}
