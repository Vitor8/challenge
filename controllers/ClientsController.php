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
        return View::make('client', [
            'request' => $request
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
}
