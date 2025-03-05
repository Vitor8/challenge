<?php

require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/User.php';

class LoginController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function home(): string {
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

        $validationError = $this->validateLoginFields($login, $password);
        if ($validationError) {
            return View::redirect('/', $validationError);
        }

        $usuario = $this->userModel->get(['login' => $login]);
        if (!$usuario || !password_verify($password, $usuario['password'])) {
            return View::redirect('/', [
                'error' => true,
                'error_message' => 'Usuário ou senha incorretos!'
            ]);
        }

        $token = bin2hex(random_bytes(32));
        setcookie('auth_token', $token, time() + 3600, '/', '', false, true);
        $this->userModel->saveToken($usuario['id'], $token);

        return View::redirect('/clientes');
    }

    public function registerView(): string {
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
        $confirmPassword = $request->input('confirm_password');

        $validationError = $this->validateRegisterFields($login, $password, $confirmPassword);
        if ($validationError) {
            return View::redirect('/cadastrar', $validationError);
        }

        $existingUser = $this->userModel->get(['login' => $login]);
        if ($existingUser) {
            return View::redirect('/cadastrar', [
                'error' => true,
                'error_message' => 'Já existe um usuário com esse nome. Cadastre outro!'
            ]);
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->userModel->create([
            'login' => $login,
            'password' => $hashedPassword
        ]);

        return View::redirect('/', [
            'success' => true,
            'success_message' => 'Usuário cadastrado com sucesso!'
        ]);
    }

    private function validateLoginFields(?string $login, ?string $password): ?array {
        if (empty($login) || empty($password)) {
            return [
                'error' => true,
                'error_message' => 'Preencha todos os campos!'
            ];
        }
        return null;
    }

    private function validateRegisterFields(?string $login, ?string $password, ?string $confirmPassword): ?array {
        if (empty($login) || empty($password) || empty($confirmPassword)) {
            return [
                'error' => true,
                'error_message' => 'Preencha todos os campos!'
            ];
        }

        if ($password !== $confirmPassword) {
            return [
                'error' => true,
                'error_message' => 'As senhas não coincidem!'
            ];
        }

        return null;
    }
}
