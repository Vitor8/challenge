<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="ms-4">
            <h2 class="mb-3">Lista de Clientes</h2>

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

            <a href="/save" class="btn btn-primary mb-3">Novo Cliente +</a>

            <table class="table table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Nome</th>
                        <th>Nascimento</th>
                        <th>CPF</th>
                        <th>RG</th>
                        <th>Telefone</th>
                        <th>Endereços</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody">
                </tbody>
            </table>

            <div class="d-flex justify-content-between">
                <button id="prevPage" class="btn btn-secondary" disabled>Anterior</button>
                <button id="nextPage" class="btn btn-secondary">Próximo</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let start = 0;
            const limit = 10;
            let totalClients = 0;

            function fetchClients() {
                $.ajax({
                    url: "/allClients",
                    type: "GET",
                    data: { start: start },
                    success: function(response) {
                        $("#clientsTableBody").empty();

                        if (response.clients.length === 0 && start > 0) {
                            start -= limit;
                            return;
                        }

                        totalClients = response.total; 

                        response.clients.forEach(client => {
                            let row = `<tr>
                                <td>${client.name}</td>
                                <td>${client.birth}</td>
                                <td>${client.cpf}</td>
                                <td>${client.rg}</td>
                                <td>${client.phone}</td>
                                <td>${client.addresses}</td>
                                <td>
                                    <i class="bi bi-pencil-square text-primary me-2 edit-client" style="cursor: pointer;" data-id="${client.id}"></i>
                                    <i class="bi bi-trash text-danger delete-client" style="cursor: pointer;" data-id="${client.id}" data-name="${client.name}"></i>
                                </td>
                            </tr>`;
                            $("#clientsTableBody").append(row);
                        });

                        $("#prevPage").prop("disabled", start === 0);
                        $("#nextPage").prop("disabled", start + limit >= totalClients);
                    }
                });
            }

            fetchClients();

            $("#nextPage").click(function() {
                if (start + limit < totalClients) {
                    start += limit;
                    fetchClients();
                }
            });


            $("#prevPage").click(function() {
                if (start > 0) {
                    start -= limit;
                    fetchClients();
                }
            });

            $(document).on("click", ".edit-client", function() {
                let clientId = $(this).data("id");
                window.location.href = `/save?id=${clientId}`;
            });


            $(document).on("click", ".delete-client", function() {
                let clientId = $(this).data("id");
                let clientName = $(this).data("name");

                Swal.fire({
                    title: `Tem certeza que deseja deletar o cliente ${clientName}?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sim, deletar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/deleteClient",
                            type: "GET",
                            data: { id: clientId },
                            success: function(response) {
                                if (response.status === "success") {
                                    Swal.fire("OK!", response.message, "success").then(() => {
                                        fetchClients();
                                    });
                                } else {
                                    Swal.fire("Erro!", response.message, "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Erro!", "Não foi possível deletar o cliente.", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>