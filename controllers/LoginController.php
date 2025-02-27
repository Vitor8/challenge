<?php
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../models/User.php';

class LoginController {
    public function home() {
        $request = new Request();

        return View::make('index', [
            'error' => $request->query('error'),
            'error_message' => $request->query('error_message')
        ]);
    }

    public function login() {
        $request = new Request();
        $login = $request->input('login');
        $password = $request->input('password');

        if (empty($login) || empty($password)) {
            return View::redirect('/', [
                'error' => true,
                'error_message' => 'Preencha todos os campos!'
            ]);
        }

        $userModel = new User();
        $usuario = $userModel->get(['login' => $login]);

        if (!$usuario) {
            return View::redirect('/', [
                'error' => true,
                'error_message' => 'Usuário não encontrado!'
            ]);
        }

        if (!password_verify($password, $usuario['password'])) {
            return View::redirect('/', [
                'error' => true,
                'error_message' => 'Senha incorreta!'
            ]);
        }

        $_SESSION['user_id'] = $usuario['id'];

        return View::redirect('/clientes');
    }

    public function registerView() {
        $request = new Request();

        return View::make('register', [
            'error' => $request->query('error'),
            'error_message' => $request->query('error_message')
        ]);
    }

    public function register() {
        $request = new Request();
        $login = $request->input('login');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');

        if (empty($login) || empty($password) || empty($confirm_password)) {
            return View::redirect('/cadastrar', [
                'error' => true,
                'error_message' => 'Preencha todos os campos!'
            ]);
        }

        if ($password !== $confirm_password) {
            return View::redirect('/cadastrar', [
                'error' => true,
                'error_message' => 'As senhas não coincidem!'
            ]);
        }

        $userModel = new User();
        $existingUser = $userModel->get(['login' => $login]);

        if ($existingUser) {
            return View::redirect('/cadastrar', [
                'error' => true,
                'error_message' => 'Já existe um usuário com esse nome. Cadastre outro!'
            ]);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $usuario = $userModel->create([
            'login' => $login,
            'password' => $hashedPassword
        ]);

        return View::redirect('/', [
            'success' => true,
            'success_message' => 'Usuário cadastrado com sucesso!'
        ]);
    }
}
