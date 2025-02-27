<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>

    <title>Login - Portal Administrativo</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px; background-color: #e5e5e5;">
        <h2 class="text-center mb-4">Login</h2>

        <?php require_once __DIR__ . '/includes/messages.php'; ?>

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

    <?php require_once __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
