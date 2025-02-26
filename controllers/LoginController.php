<?php
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../models/User.php';

class LoginController {
    public function home() {
        $request = new Request();

        echo View::make('index', [
            'erro' => $request->query('erro'),
            'error_message' => $request->query('error_message')
        ]);
    }

    public function login() {
        $request = new Request();
        $login = $request->input('login');
        $password = $request->input('password');

        if (empty($login) || empty($password)) {
            return View::redirect('/', [
                'erro' => true,
                'error_message' => 'Preencha todos os campos!'
            ]);
        }

        $userModel = new User();
        $usuario = $userModel->get([
            'login' => $login,
            'password' => $password
        ]);

        if ($usuario) {
            return View::redirect('/home');
        } else {
            return View::redirect('/', [
                'erro' => true,
                'error_message' => 'Usuário não encontrado!'
            ]);
        }
    }
}
