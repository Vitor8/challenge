<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 500px; background-color: #e5e5e5;">
        <h2 class="text-center mb-4">Novo Cliente</h2>

        <form action="#" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Digite o nome completo">
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="birth" class="form-label">Data de Nascimento</label>
                    <input type="text" id="birth" name="birth" class="form-control" placeholder="DD/MM/AAAA">
                </div>
                <div class="col-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="rg" class="form-label">RG</label>
                    <input type="text" id="rg" name="rg" class="form-control" placeholder="00.000.000-0">
                </div>
                <div class="col-6">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="(00) 00000-0000">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Cadastrar Cliente</button>
        </form>

        <p class="text-center mt-3">
            <a href="/clientes" class="btn btn-secondary w-100">Voltar</a>
        </p>
    </div>

    <script>
        $(document).ready(function(){
            $("#birth").inputmask("99/99/9999"); 
            $("#cpf").inputmask("999.999.999-99"); 
            $("#rg").inputmask("99.999.999-9");
            $("#phone").inputmask("(99) 99999-9999"); 
        });
    </script>
</body>
</html>
