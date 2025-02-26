<?php
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../database/database.php';

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
        $senha = $request->input('senha');

        if (empty($login) || empty($senha)) {
            return View::redirect('/', [
                'erro' => true,
                'error_message' => 'Preencha todos os campos!'
            ]);
        }

        try {
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE login = :login AND senha = :senha");
            $stmt->execute(['login' => $login, 'senha' => $senha]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                return View::redirect('/home');
            } else {
                return View::redirect('/', [
                    'erro' => true,
                    'error_message' => 'Usuário não encontrado!'
                ]);
            }
        } catch (PDOException $e) {
            return View::redirect('/', [
                'erro' => true,
                'error_message' => 'Erro ao conectar com o banco de dados!'
            ]);
        }
    }
}
