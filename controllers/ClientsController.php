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

    /**
     * Initializes the ClientsController with required models.
     */
    public function __construct() {
        $this->clientModel = new Client();
        $this->addressModel = new Address();
        $this->clientAddressModel = new ClientAddress();
    }

    /**
     * Displays the client list page.
     *
     * @return string The rendered HTML view.
     */
    public function list(): string {
        return View::make('list', ['request' => new Request()]);
    }

    /**
     * Displays the client creation/editing form.
     *
     * @return string The rendered HTML view.
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
     * Creates a new client and their associated addresses.
     *
     * @return void Redirects to the client list view with a success message.
     */
    public function create(): void {
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
            View::redirect('/save', $validationError);
            return;
        }

        if ($this->clientModel->get(['cpf' => $clientData['cpf']]) || $this->clientModel->get(['rg' => $clientData['rg']])) {
            View::redirect('/save', [
                'error' => true,
                'error_message' => 'Já existe um usuário com este RG ou CPF cadastrados!'
            ]);
            return;
        }

        $clientData['birth'] = DateTime::createFromFormat('d/m/Y', $clientData['birth'])->format('Y-m-d');
        $client = $this->clientModel->create($clientData);

        $this->handleClientAddresses($client->id, $request);

        View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Cliente {$clientData['name']} cadastrado com sucesso!"
        ]);
    }

    /**
     * Edits an existing client and updates their addresses.
     *
     * @return void Redirects to the client list view with a success message.
     */
    public function edit(): void {
        $request = new Request();
        $idClient = $request->input('id');

        $validationError = $this->validateClientId($idClient);
        if ($validationError) {
            View::redirect('/clientes', $validationError);
            return;
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

        View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Cliente {$clientData['name']} atualizado com sucesso!"
        ]);
    }

    /**
     * Retrieves all clients with their addresses in JSON format.
     *
     * @return void Outputs JSON response.
     */
    public function allClients(): void {
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
     * Deletes a client from the database.
     *
     * @return void Outputs JSON response.
     */
    public function delete(): void {
        $request = new Request();
        $clientId = $request->query('id');

        if (!$clientId) {
            $this->jsonResponse('error', 'ID do cliente não fornecido.');
            return;
        }

        $client = $this->clientModel->get(['id' => $clientId]);
        if (!$client) {
            $this->jsonResponse('error', 'Cliente não encontrado.');
            return;
        }

        $deleted = $this->clientModel->deleteClient($clientId);

        if ($deleted) {
            $this->jsonResponse('success', "Cliente {$client['name']} foi removido com sucesso!");
        } else {
            $this->jsonResponse('error', 'Erro ao deletar cliente.');
        }
    }

    /**
     * Validates client fields and addresses.
     *
     * @param array $clientData Client data.
     * @param array $addresses List of addresses.
     * @return array|null Validation error message or null if valid.
     */
    private function validateClientFields(array $clientData, array $addresses): ?array {
        foreach ($clientData as $key => $value) {
            if (empty($value)) {
                return [
                    'error' => true,
                    'error_message' => 'Preencha todos os campos obrigatórios!'
                ];
            }
        }

        if (empty($addresses[0])) {
            return [
                'error' => true,
                'error_message' => 'O primeiro endereço é obrigatório!'
            ];
        }

        return null;
    }

    /**
     * Validates the client ID.
     *
     * @param string|null $idClient The client ID.
     * @return array|null Validation error message or null if valid.
     */
    private function validateClientId(?string $idClient): ?array {
        if (!$idClient) {
            return [
                'error' => true,
                'error_message' => 'ID do cliente não foi informado!'
            ];
        }

        if (!$this->clientModel->get(['id' => $idClient])) {
            return [
                'error' => true,
                'error_message' => 'Cliente não encontrado!'
            ];
        }

        return null;
    }

    /**
     * Handles client address creation.
     *
     * @param int $clientId The client ID.
     * @param Request $request The request object.
     * @return void
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
