<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Marcenaria</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container text-center">
        <div class="py-5">
            <h1 class="display-5 fw-bold text-dark">Painel de Controle</h1>
            <p class="fs-5 text-muted">Gestão Integrada de Clientes e Serviços da Marcenaria</p>
            
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mt-4">
                <a href="cadastrar_cliente.php" class="btn btn-primary btn-lg px-4 gap-3">Gerenciar Clientes</a>
                <a href="cadastrar_servico.php" class="btn btn-outline-secondary btn-lg px-4">Gerenciar Serviços</a>
                <a href="gerar_orcamento.php" class="btn btn-primary btn-lg m-2">Gerar Novo Orçamento</a>
                <a href="listar_orcamentos.php" class="btn btn-outline-primary btn-lg m-2">Histórico de Orçamentos</a>
            </div>
        </div>
    </div>
</body>
</html>