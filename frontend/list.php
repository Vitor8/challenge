<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

            <a href="/save" class="btn btn-primary">Novo Cliente +</a>
        </div>
    </div>
</body>
</html>