<?php
require_once __DIR__ . '/../../core/DB.php';

class PopulateClientsAddresses {
    private PDO $pdo;
    private string $jsonPath;

    public function __construct() {
        $this->pdo = DB::getConnection();
        $this->jsonPath = __DIR__ . '/../clients_data.json';
    }

    /**
     * Runs the migration to populate the clients, addresses, and client_address tables.
     */
    public function up(): void {
        if (!file_exists($this->jsonPath)) {
            die("❌ JSON file not found: {$this->jsonPath}\n");
        }

        $clientsData = json_decode(file_get_contents($this->jsonPath), true);
        if (!$clientsData) {
            die("❌ Error decoding JSON data.\n");
        }

        try {
            $this->pdo->beginTransaction();

            foreach ($clientsData as $client) {
                $stmt = $this->pdo->prepare("INSERT INTO clients (name, birth, cpf, rg, phone) 
                                             VALUES (:name, :birth, :cpf, :rg, :phone)");
                $stmt->execute([
                    'name' => $client['name'],
                    'birth' => $client['birth'],
                    'cpf' => $client['cpf'],
                    'rg' => $client['rg'],
                    'phone' => $client['phone']
                ]);
                $clientId = $this->pdo->lastInsertId();

                foreach ($client['addresses'] as $address) {
                    $stmt = $this->pdo->prepare("INSERT INTO addresses (street, number, district, city, state, zip_code) 
                                                 VALUES (:street, :number, :district, :city, :state, :zip_code)");
                    $stmt->execute([
                        'street' => $address['street'],
                        'number' => $address['number'],
                        'district' => $address['district'],
                        'city' => $address['city'],
                        'state' => $address['state'],
                        'zip_code' => $address['zip_code']
                    ]);
                    $addressId = $this->pdo->lastInsertId();

                    $stmt = $this->pdo->prepare("INSERT INTO client_address (client_id, address_id) 
                                                 VALUES (:client_id, :address_id)");
                    $stmt->execute([
                        'client_id' => $clientId,
                        'address_id' => $addressId
                    ]);
                }
            }

            $this->pdo->commit();
            echo "✅ Successfully populated clients, addresses, and client_address tables.\n";

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            die("❌ Error populating tables: " . $e->getMessage() . "\n");
        }
    }

    /**
     * Reverts the migration by deleting the inserted data.
     */
    public function down(): void {
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec("DELETE FROM client_address");
            $this->pdo->exec("DELETE FROM addresses");
            $this->pdo->exec("DELETE FROM clients");
            $this->pdo->commit();
            echo "✅ Successfully reverted populated data.\n";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            die("❌ Error reverting data: " . $e->getMessage() . "\n");
        }
    }
}
