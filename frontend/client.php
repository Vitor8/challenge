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
        <h2 class="text-center mb-4"><?php echo isset($client) ? "Editar Cliente" : "Novo Cliente"; ?></h2>

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

        <form action="<?php echo isset($client['id']) ? '/edit?id=' . $client['id'] : '/create'; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $client['id'] ?? ''; ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" id="name" name="name" class="form-control" 
                    value="<?php echo $client['data']['name'] ?? ''; ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="birth" class="form-label">Data de Nascimento</label>
                    <input type="text" id="birth" name="birth" class="form-control"
                        value="<?php echo $client['data']['birth'] ?? ''; ?>" required>
                </div>
                <div class="col-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control"
                        value="<?php echo $client['data']['cpf'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label for="rg" class="form-label">RG</label>
                    <input type="text" id="rg" name="rg" class="form-control"
                        value="<?php echo $client['data']['rg'] ?? ''; ?>" required>
                </div>
                <div class="col-6">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                        value="<?php echo $client['data']['phone'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="mb-2">
                <label class="form-label">Endereços: 
                    <i class="bi bi-plus-lg" id="addAddress" style="font-size: 10px; color: white; background-color: #87CEFA; padding: 6px; border-radius: 4px; cursor: pointer;"></i>
                </label>
            </div>

            <div id="addressContainer">
                <?php if (isset($client['data']['addresses']) && count($client['data']['addresses']) > 0): ?>
                    <?php foreach ($client['data']['addresses'] as $address): ?>
                        <div class="card p-3 mb-3 address-card">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <i class="bi bi-trash-fill text-danger remove-address"></i>
                                </span>
                                <h6 class="text-primary">Endereço</h6>
                            </div>
                            <div class="row mb-2">
                                <div class="col-10">
                                    <label class="form-label">CEP</label>
                                    <input type="text" name="zip[]" class="form-control zip" 
                                        value="<?php echo $address['zip_code']; ?>" required>
                                </div>
                                <div class="col-2">
                                    <label class="form-label">Estado</label>
                                    <select name="state[]" class="form-control" required>
                                        <?php
                                        $states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 
                                                'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 
                                                'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                                        foreach ($states as $state) {
                                            $selected = ($state === $address['state']) ? 'selected' : '';
                                            echo "<option value='$state' $selected>$state</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-9">
                                    <label class="form-label">Rua</label>
                                    <input type="text" name="street[]" class="form-control" 
                                        value="<?php echo $address['street']; ?>" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Número</label>
                                    <input type="text" name="number[]" class="form-control number-input" 
                                        value="<?php echo $address['number']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="form-label">Bairro</label>
                                    <input type="text" name="district[]" class="form-control"
                                        value="<?php echo $address['district']; ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" name="city[]" class="form-control" 
                                        value="<?php echo $address['city']; ?>" required>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card p-3 mb-3 address-card">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-trash-fill text-danger d-none remove-address"></i></span>
                            <h6 class="text-primary">Endereço</h6>
                        </div>
                        <div class="row mb-2">
                            <div class="col-10">
                                <label class="form-label">CEP</label>
                                <input type="text" name="zip[]" class="form-control zip" placeholder="00000-000" required>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Estado</label>
                                <select name="state[]" class="form-control" required>
                                    <option value="">UF</option>
                                    <?php
                                    $states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 
                                            'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 
                                            'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                                    foreach ($states as $state) {
                                        echo "<option value='$state'>$state</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-9">
                                <label class="form-label">Rua</label>
                                <input type="text" name="street[]" class="form-control" placeholder="Digite a rua" required>
                            </div>
                            <div class="col-3">
                                <label class="form-label">Número</label>
                                <input type="text" name="number[]" class="form-control number-input" placeholder="Nº" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="form-label">Bairro</label>
                                <input type="text" name="district[]" class="form-control" placeholder="Digite o bairro" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="city[]" class="form-control" placeholder="Digite a cidade" required>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <?php echo isset($client['id']) ? 'Editar Cliente' : 'Cadastrar Cliente'; ?>
            </button>

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
