<?php
require_once __DIR__ . '/../core/Request.php';

$request = new Request();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px; background-color: #e5e5e5;">
        <h2 class="text-center mb-4">Login</h2>

        <?php if ($request->query('erro')): ?>
            <div class="alert alert-danger text-center w-100">
                <?php echo $request->query('error_message'); ?>
            </div>
        <?php endif; ?>
        <?php if ($request->query('success')): ?>
            <div class="alert alert-success text-center w-100">
                <?php echo $request->query('success_message'); ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" id="login" name="login" class="form-control" placeholder="Digite seu login">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Digite sua senha">
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <p class="text-center mt-3">
            <a href="/cadastrar">Não é cadastrado? Clique aqui para registrar-se</a>
        </p>
    </div>
</body>
</html>
