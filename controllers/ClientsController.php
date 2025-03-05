<?php

require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Address.php';
require_once __DIR__ . '/../models/ClientAddress.php';

class ClientsController {
    private Client $clientModel;
    private Address $addressModel;
    private ClientAddress $clientAddressModel;

    public function __construct() {
        $this->clientModel = new Client();
        $this->addressModel = new Address();
        $this->clientAddressModel = new ClientAddress();
    }

    /**
     * Displays the list of clients.
     */
    public function list(): string {
        return View::make('list', ['request' => new Request()]);
    }

    /**
     * Loads the client creation/editing form.
     */
    public function save(): string {
        $request = new Request();
        $clientId = $request->query('id');

        $client = null;
        if ($clientId) {
            $clientData = $this->clientModel->getClientDataById(['id' => $clientId]);

            if ($clientData) {
                $client = [
                    'id' => $clientId,
                    'data' => $clientData
                ];
            }
        }

        return View::make('client', [
            'request' => $request,
            'client' => $client
        ]);
    }

    /**
     * Handles the creation of a new client.
     */
    public function create() {
        $request = new Request();
        $clientData = [
            'name' => $request->input('name'),
            'birth' => $request->input('birth'),
            'cpf' => $request->input('cpf'),
            'rg' => $request->input('rg'),
            'phone' => $request->input('phone')
        ];
        $addresses = $request->input('zip');

        $validationError = $this->validateClientFields($clientData, $addresses);
        if ($validationError) {
            return View::redirect('/save', $validationError);
        }

        if ($this->clientModel->get(['cpf' => $clientData['cpf']]) || $this->clientModel->get(['rg' => $clientData['rg']])) {
            return View::redirect('/save', [
                'error' => true,
                'error_message' => 'A user with this RG or CPF already exists!'
            ]);
        }

        $clientData['birth'] = DateTime::createFromFormat('d/m/Y', $clientData['birth'])->format('Y-m-d');
        $client = $this->clientModel->create($clientData);

        $this->handleClientAddresses($client->id, $request);

        return View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Client {$clientData['name']} successfully registered!"
        ]);
    }

    /**
     * Handles client editing.
     */
    public function edit() {
        $request = new Request();
        $idClient = $request->input('id');

        $validationError = $this->validateClientId($idClient);
        if ($validationError) {
            return View::redirect('/clientes', $validationError);
        }

        $clientData = [
            'id' => $idClient,
            'name' => $request->input('name'),
            'birth' => date('Y-m-d', strtotime(str_replace('/', '-', $request->input('birth')))),
            'cpf' => $request->input('cpf'),
            'rg' => $request->input('rg'),
            'phone' => $request->input('phone')
        ];
        $addresses = $request->input('zip');

        $this->clientModel->edit($clientData);
        $this->clientAddressModel->deleteByClientId($idClient);
        $this->addressModel->deleteByClientId($idClient);
        $this->handleClientAddresses($idClient, $request);

        return View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Client {$clientData['name']} successfully updated!"
        ]);
    }

    /**
     * Returns a JSON response with all paginated clients.
     */
    public function allClients() {
        $request = new Request();
        $start = $request->query('start') ?? 0;
        $limit = 10;

        $clients = $this->clientModel->getAllWithAddresses($start, $limit);
        $totalClients = $this->clientModel->countClients();

        header('Content-Type: application/json');
        echo json_encode([
            'clients' => $clients,
            'total' => $totalClients
        ]);
    }

    /**
     * Handles client deletion.
     */
    public function delete() {
        $request = new Request();
        $clientId = $request->query('id');

        if (!$clientId) {
            return $this->jsonResponse('error', 'Client ID not provided.');
        }

        $client = $this->clientModel->get(['id' => $clientId]);
        if (!$client) {
            return $this->jsonResponse('error', 'Client not found.');
        }

        $deleted = $this->clientModel->deleteClient($clientId);

        if ($deleted) {
            return $this->jsonResponse('success', "Client {$client['name']} successfully removed!");
        } else {
            return $this->jsonResponse('error', 'Error deleting client.');
        }
    }

    /**
     * Validates client fields before saving.
     */
    private function validateClientFields(array $clientData, array $addresses): ?array {
        foreach ($clientData as $key => $value) {
            if (empty($value)) {
                return [
                    'error' => true,
                    'error_message' => 'Fill in all required fields!'
                ];
            }
        }

        if (empty($addresses[0])) {
            return [
                'error' => true,
                'error_message' => 'The first address is required!'
            ];
        }

        return null;
    }

    /**
     * Validates client ID before updating or deleting.
     */
    private function validateClientId(?string $idClient): ?array {
        if (!$idClient) {
            return [
                'error' => true,
                'error_message' => 'Client ID was not provided!'
            ];
        }

        if (!$this->clientModel->get(['id' => $idClient])) {
            return [
                'error' => true,
                'error_message' => 'Client not found!'
            ];
        }

        return null;
    }

    /**
     * Handles storing client addresses.
     */
    private function handleClientAddresses(int $clientId, Request $request): void {
        foreach ($request->input('zip') as $key => $zip) {
            if (!empty($zip)) {
                $address = $this->addressModel->create([
                    'zip_code' => $zip,
                    'state' => $request->input('state')[$key],
                    'street' => $request->input('street')[$key],
                    'number' => $request->input('number')[$key],
                    'district' => $request->input('district')[$key],
                    'city' => $request->input('city')[$key]
                ]);

                $this->clientAddressModel->create([
                    'client_id' => $clientId,
                    'address_id' => $address->id
                ]);
            }
        }
    }

    /**
     * Returns a standardized JSON response.
     */
    private function jsonResponse(string $status, string $message) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
    }
}
