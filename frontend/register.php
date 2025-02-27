<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
    
    <title>Cadastro - Portal Administrativo</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px; background-color: #e5e5e5;">
        <h2 class="text-center mb-4">Cadastro</h2>

        <?php require_once __DIR__ . '/includes/messages.php'; ?>

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

    <?php require_once __DIR__ . '/includes/scripts.php'; ?>

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
