<?php
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Address.php';
require_once __DIR__ . '/../models/ClientAddress.php';

class ClientsController {
    public function list() {
        $request = new Request();
        return View::make('list', [
            'request' => $request
        ]);
    }

    public function save() {
        $request = new Request();
        $clientId = $request->query('id');
    
        $client = null;
        if ($clientId) {
            $clientModel = new Client();
            $clientData = $clientModel->getClientDataById(['id' => $clientId]);
    
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
        
        $name = $request->input('name');
        $birth = $request->input('birth');
        $cpf = $request->input('cpf');
        $rg = $request->input('rg');
        $phone = $request->input('phone');
        $addresses = $request->input('zip');

        if (empty($name) || empty($birth) || empty($cpf) || empty($rg) || empty($phone) || empty($addresses[0])) {
            return View::redirect('/save', [
                'error' => true,
                'error_message' => 'Preencha todos os campos obrigatórios!'
            ]);
        }

        $clientModel = new Client();
        
        $existingClient = $clientModel->get([
            'cpf' => $cpf
        ]) ?? $clientModel->get([
            'rg' => $rg
        ]);
        
        if ($existingClient) {
            return View::redirect('/save', [
                'error' => true,
                'error_message' => 'Já existe um usuário com este RG ou CPF cadastrados!'
            ]);
        }

        $client = $clientModel->create([
            'name' => $name,
            'birth' => DateTime::createFromFormat('d/m/Y', $birth)->format('Y-m-d'),
            'cpf' => $cpf,
            'rg' => $rg,
            'phone' => $phone
        ]);

        $clientId = $client->id;

        $addressModel = new Address();
        $clientAddressModel = new ClientAddress();

        foreach ($request->input('zip') as $key => $zip) {
            if (!empty($zip)) {
                $address = $addressModel->create([
                    'street' => $request->input("street")[$key],
                    'number' => $request->input("number")[$key],
                    'district' => $request->input("district")[$key],
                    'city' => $request->input("city")[$key],
                    'state' => $request->input("state")[$key],
                    'zip_code' => $zip
                ]);

                $clientAddressModel->create([
                    'client_id' => $clientId,
                    'address_id' => $address->id
                ]);
            }
        }

        return View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Cliente {$name} cadastrado com sucesso!"
        ]);
    }

    public function edit() {
        $request = new Request();
        $idClient = $request->input('id');

        if (!$idClient) {
            return View::redirect('/clientes', [
                'error' => true,
                'error_message' => 'ID do cliente não foi informado!'
            ]);
        }

        $clientModel = new Client();

        $existingClient = $clientModel->get(['id' => $idClient]);

        if (!$existingClient) {
            return View::redirect('/clientes', [
                'error' => true,
                'error_message' => 'Cliente não encontrado!'
            ]);
        }

        $name = $request->input('name');
        $birth = $request->input('birth');
        $cpf = $request->input('cpf');
        $rg = $request->input('rg');
        $phone = $request->input('phone');
        $addresses = $request->input('zip');

        $clientModel->edit([
            'id' => $idClient,
            'name' => $name,
            'birth' => date('Y-m-d', strtotime(str_replace('/', '-', $birth))),
            'cpf' => $cpf,
            'rg' => $rg,
            'phone' => $phone
        ]);

        $addressModel = new Address();
        $clientAddressModel = new ClientAddress();
        $clientAddressModel->deleteByClientId($idClient);
        $addressModel->deleteByClientId($idClient);

        foreach ($addresses as $key => $zip) {
            $newAddress = $addressModel->create([
                'zip_code' => $zip,
                'state' => $request->input('state')[$key],
                'street' => $request->input('street')[$key],
                'number' => $request->input('number')[$key],
                'district' => $request->input('district')[$key],
                'city' => $request->input('city')[$key]
            ]);

            $clientAddressModel->create([
                'client_id' => $idClient,
                'address_id' => $newAddress->id
            ]);
        }

        return View::redirect('/clientes', [
            'success' => true,
            'success_message' => "Cliente {$name} atualizado com sucesso!"
        ]);
    }

    public function allClients() {
        $request = new Request();
        $start = $request->query('start') ?? 0;
        $limit = 10; 
    
        $clientModel = new Client();
        $clients = $clientModel->getAllWithAddresses($start, $limit);
        $totalClients = $clientModel->countClients();
    
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
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'ID do cliente não fornecido.']);
            return;
        }
    
        $clientModel = new Client();
        $client = $clientModel->get(['id' => $clientId]);
    
        if (!$client) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Cliente não encontrado.']);
            return;
        }
    
        $deleted = $clientModel->delete($clientId);
    
        if ($deleted) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "Cliente {$client['name']} foi removido com sucesso!"]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Erro ao deletar cliente.']);
        }
    }
}
