<?php
require_once __DIR__ . '/../database/database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$login = $input['login'] ?? '';
$senha = $input['senha'] ?? '';

if (empty($login) || empty($senha)) {
    echo json_encode(["status" => 0, "message" => "Preencha todos os campos"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE login = :login AND senha = :senha");
    $stmt->execute(['login' => $login, 'senha' => $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode(["status" => 1]);
    } else {
        echo json_encode(["status" => 0, "message" => "Usuário não encontrado"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => 0, "message" => "Erro no banco de dados"]);
}
