<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>

    <style>
        body {
            min-height: 100vh;
            overflow-y: auto;
        }

        .card {
            margin: 20px auto; 
        }
    </style>
</head>
<body class="bg-light">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 500px; background-color: #f8f9fa;">
        <h2 class="text-center mb-4">Novo Cliente</h2>

        <?php if (isset($request) && $request->query('error')): ?>
            <div class="alert alert-danger text-center w-100">
                <?php echo $request->query('error_message'); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($request) && $request->query('success')): ?>
            <div class="alert alert-success text-center w-100">
                <?php echo $request->query('success_message'); ?>
            </div>
        <?php endif; ?>

        <form action="/create" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Digite o nome completo" required>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="birth" class="form-label">Data de Nascimento</label>
                    <input type="text" id="birth" name="birth" class="form-control" placeholder="DD/MM/AAAA" required>
                </div>
                <div class="col-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" placeholder="000.000.000-00" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="rg" class="form-label">RG</label>
                    <input type="text" id="rg" name="rg" class="form-control" placeholder="00.000.000-0" required>
                </div>
                <div class="col-6">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="(00) 00000-0000" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label">Endereços: 
                    <i class="bi bi-plus-lg" id="addAddress" style="font-size: 10px; color: white; background-color: #87CEFA; padding: 6px; border-radius: 4px; cursor: pointer;"></i>
                </label>
            </div>

            <div id="addressContainer">
                <div class="card p-3 mb-3 address-card">
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-trash-fill text-danger d-none remove-address"></i></span>
                        <h6 class="text-primary">Novo Endereço</h6>
                    </div>
                    <div class="row mb-2">
                        <div class="col-10">
                            <label for="zip[]" class="form-label">CEP</label>
                            <input type="text" name="zip[]" class="form-control zip" placeholder="00000-000" required>
                        </div>
                        <div class="col-2">
                            <label for="state[]" class="form-label">Estado</label>
                            <select name="state[]" class="form-control" required>
                                <option value="">UF</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-9">
                            <label for="street[]" class="form-label">Rua</label>
                            <input type="text" name="street[]" class="form-control" placeholder="Digite a rua" required>
                        </div>
                        <div class="col-3">
                            <label for="number[]" class="form-label">Número</label>
                            <input type="text" name="number[]" class="form-control number-input" placeholder="Nº" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label for="district[]" class="form-label">Bairro</label>
                            <input type="text" name="district[]" class="form-control" placeholder="Digite o bairro" required>
                        </div>
                        <div class="col-6">
                            <label for="city[]" class="form-label">Cidade</label>
                            <input type="text" name="city[]" class="form-control" placeholder="Digite a cidade" required>
                        </div>
                    </div>
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
            function applyMasks() {
                $(".zip").inputmask("99999-999");
                $(".number-input").inputmask({ regex: "^[0-9]+$", placeholder: "" });
                $("#birth").inputmask("99/99/9999");
                $("#cpf").inputmask("999.999.999-99");
                $("#rg").inputmask("99.999.999-9");
                $("#phone").inputmask("(99) 99999-9999");
            }

            applyMasks();

            $("#addAddress").click(function(){
                let newCard = $(".address-card:first").clone();
                newCard.find("input").val("");
                newCard.find(".remove-address").removeClass("d-none");
                $("#addressContainer").append(newCard);
                applyMasks();
            });

            $(document).on("click", ".remove-address", function(){
                if ($(".address-card").length > 1) {
                    $(this).closest(".address-card").remove();
                }
            });
        });
    </script>

</body>
</html>
