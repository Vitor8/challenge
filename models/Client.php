<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/ClientAddress.php';
require_once __DIR__ . '/Address.php';

/**
 * Client Model - Handles database operations related to clients.
 */
class Client extends BaseModel {

    /**
     * Initializes the Client model with the associated database table.
     */
    public function __construct() {
        parent::__construct('clients');
    }

    /**
     * Retrieves all clients along with their formatted addresses.
     *
     * @param int $start The starting index for pagination.
     * @param int $limit The number of clients to fetch.
     * @return array Returns an array of clients with their formatted addresses.
     */
    public function getAllWithAddresses(int $start, int $limit): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.name, DATE_FORMAT(c.birth, '%d/%m/%Y') AS birth, c.cpf, c.rg, c.phone
                FROM clients c
                ORDER BY c.id DESC
                LIMIT :start, :limit
            ");

            $stmt->bindValue(':start', $start, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->attachFormattedAddresses($clients);
        } catch (PDOException $e) {
            error_log("Error retrieving clients: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts the total number of clients in the database.
     *
     * @return int Returns the total count of clients.
     */
    public function countClients(): int {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM clients");
            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            error_log("Error counting clients: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Deletes a client along with its associated addresses.
     *
     * @param int $clientId The ID of the client to delete.
     * @return bool Returns true if deletion was successful, false otherwise.
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
            error_log("Error deleting client ID $clientId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves client details by ID, including their addresses.
     *
     * @param array $filters An array of filters (e.g., ['id' => client_id]).
     * @return array|null Returns an array with client details and addresses, or null if not found.
     */
    public function getClientDataById(array $filters): ?array {
        try {
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
        } catch (PDOException $e) {
            error_log("Error retrieving client ID {$filters['id']}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updates client information.
     *
     * @param array $data An associative array with updated client data.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function edit(array $data): bool {
        return parent::edit($data);
    }

    /**
     * Attaches formatted addresses to the client data.
     *
     * @param array $clients The list of clients to which addresses should be attached.
     * @return array Returns the list of clients with formatted addresses.
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
