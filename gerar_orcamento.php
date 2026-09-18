<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $servico_id = $_POST['servico_id'];
    $quantidade = $_POST['quantidade'];

    // Buscar o valor unitario usando a coluna preco_base
    $query_servico = "SELECT preco_base FROM servicos WHERE id = '$servico_id'";
    $res_servico = mysqli_query($conn, $query_servico);
    $servico = mysqli_fetch_assoc($res_servico);

    if ($servico) {
        $valor_unitario = $servico['preco_base'];
        $valor_total = $valor_unitario * $quantidade;
        $data_orcamento = date('Y-m-d');

        $sql = "INSERT INTO orcamentos (cliente_id, servico_id, quantidade, valor_total, data_orcamento) 
                VALUES ('$cliente_id', '$servico_id', '$quantidade', '$valor_total', '$data_orcamento')";

        if (mysqli_query($conn, $sql)) {
            $mensagem = "<div class='alert alert-success'>Orçamento gerado com sucesso! Valor Total: R$ " . number_format($valor_total, 2, ',', '.') . "</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao gerar orçamento: " . mysqli_error($conn) . "</div>";
        }
    }
}

// Buscar clientes e serviços para os campos de seleção
$clientes = mysqli_query($conn, "SELECT id, nome FROM clientes ORDER BY nome ASC");
$servicos = mysqli_query($conn, "SELECT id, nome_servico, preco_base FROM servicos ORDER BY nome_servico ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerar Orçamento - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Gerar Novo Orçamento</h3>
            </div>
            <div class="card-body">
                <?php echo $mensagem; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Cliente:</label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Selecione o Cliente</option>
                            <?php while ($c = mysqli_fetch_assoc($clientes)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Serviço / Item:</label>
                        <select name="servico_id" class="form-select" required>
                            <option value="">Selecione o Serviço</option>
                            <?php while ($s = mysqli_fetch_assoc($servicos)): ?>
                                <option value="<?php echo $s['id']; ?>">
                                    <?php echo htmlspecialchars($s['nome_servico']) . " (R$ " . number_format($s['preco_base'], 2, ',', '.') . ")"; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantidade:</label>
                        <input type="number" name="quantidade" class="form-control" value="1" min="1" required>
                    </div>

                    <button type="submit" class="btn btn-success">Calcular e Gerar Orçamento</button>
                    <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>