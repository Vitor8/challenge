<?php
require_once __DIR__ . '/../core/Request.php';

$request = new Request();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Portal Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px; background-color: #e5e5e5;">
        <h2 class="text-center mb-4">Cadastro</h2>

        <?php if ($request->query('error')): ?>
            <div class="alert alert-danger text-center w-100">
                <?php echo $request->query('error_message'); ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" id="registerForm">
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" id="login" name="login" class="form-control" placeholder="Digite seu login">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Digite sua senha">
                <small class="text-danger d-none" id="passwordError">Senha precisa ter pelo menos 8 caracteres</small>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirme a senha</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirme sua senha">
                <small class="text-danger d-none" id="confirmPasswordError">As senhas não coincidem</small>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="registerBtn" disabled>Cadastrar Usuário</button>
        </form>

        <p class="text-center mt-3">
            <a href="/">Já tem uma conta? Faça login</a>
        </p>
    </div>

    <script>
        $(document).ready(function() {
            function validateForm() {
                let password = $("#password").val();
                let confirmPassword = $("#confirm_password").val();
                let isValid = true;

                if (password.length < 8) {
                    $("#passwordError").removeClass("d-none");
                    isValid = false;
                } else {
                    $("#passwordError").addClass("d-none");
                }

                if (password !== confirmPassword) {
                    $("#confirmPasswordError").removeClass("d-none");
                    isValid = false;
                } else {
                    $("#confirmPasswordError").addClass("d-none");
                }

                $("#registerBtn").prop("disabled", !isValid);
            }

            $("#password, #confirm_password").on("input", validateForm);
        });
    </script>
</body>
</html>
