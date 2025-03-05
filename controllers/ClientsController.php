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

    public function list(): string {
        return View::make('list', ['request' => new Request()]);
    }

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
                'error_message' => 'Já existe um usuário com este RG ou CPF cadastrados!'
            ]);
        }

        $clientData['birth'] = DateTime::createFromFormat('d/m/Y', $clientData['birth'])->format('Y-m-d');
        $client = $this->clientModel->create($clientData);

        $this->handleClientAddresses($client->id, $request);

        return View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Cliente {$clientData['name']} cadastrado com sucesso!"
        ]);
    }

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
            'success_message' => "Cliente {$clientData['name']} atualizado com sucesso!"
        ]);
    }

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

    public function delete() {
        $request = new Request();
        $clientId = $request->query('id');

        if (!$clientId) {
            return $this->jsonResponse('error', 'ID do cliente não fornecido.');
        }

        $client = $this->clientModel->get(['id' => $clientId]);
        if (!$client) {
            return $this->jsonResponse('error', 'Cliente não encontrado.');
        }

        $deleted = $this->clientModel->delete($clientId);

        if ($deleted) {
            return $this->jsonResponse('success', "Cliente {$client['name']} foi removido com sucesso!");
        } else {
            return $this->jsonResponse('error', 'Erro ao deletar cliente.');
        }
    }

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

    private function jsonResponse(string $status, string $message) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
    }
}
