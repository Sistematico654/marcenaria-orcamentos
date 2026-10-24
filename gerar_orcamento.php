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
            $mensagem = "<div class='alert alert-success' role='alert'>Orçamento gerado com sucesso! Valor Total: R$ " . number_format($valor_total, 2, ',', '.') . "</div>";
        } else {
            $mensagem = "<div class='alert alert-danger' role='alert'>Erro ao gerar orçamento: " . mysqli_error($conn) . "</div>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Orçamento - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Ajustado py-3 e py-md-5 para otimizar o espaço vertical no celular -->
    <div class="container py-3 py-md-5">
        <div class="card shadow border-0">
            <!-- Título responsivo e padding ajustado -->
            <div class="card-header bg-primary text-white py-3">
                <h3 class="mb-0 fs-5 fs-md-3 fw-bold">📄 Gerar Novo Orçamento</h3>
            </div>
            <div class="card-body p-3 p-md-4">
                <?php echo $mensagem; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente:</label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Selecione o Cliente</option>
                            <?php while ($c = mysqli_fetch_assoc($clientes)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Serviço / Item:</label>
                        <select id="servico_id" name="servico_id" class="form-select" onchange="calcularTotal()" required>
                            <option value="" data-preco="0">Selecione o Serviço</option>
                            <?php while ($s = mysqli_fetch_assoc($servicos)): ?>
                                <option value="<?php echo $s['id']; ?>" data-preco="<?php echo $s['preco_base']; ?>">
                                    <?php echo htmlspecialchars($s['nome_servico']) . " (R$ " . number_format($s['preco_base'], 2, ',', '.') . ")"; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantidade:</label>
                        <input type="number" id="quantidade" name="quantidade" class="form-control" value="1" min="1" oninput="calcularTotal()" required>
                    </div>

                    <!-- Exibição prévia do valor total em tempo real -->
                    <div class="alert alert-info fw-bold fs-6 fs-md-5">
                        Valor Total Estimado: <span id="valor_total_preview">R$ 0,00</span>
                    </div>

                    <!-- d-grid faz o botão ocupar a largura ideal em telas mobile -->
                    <div class="d-grid d-md-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar e Registrar Orçamento</button>
                        <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script JavaScript / Cálculo Dinâmico (Mantido idêntico ao original) -->
    <script>
    function calcularTotal() {
        const selectServico = document.getElementById('servico_id');
        const inputQuantidade = document.getElementById('quantidade');
        const previewTotal = document.getElementById('valor_total_preview');

        const optionSelecionada = selectServico.options[selectServico.selectedIndex];
        const precoBase = parseFloat(optionSelecionada.getAttribute('data-preco')) || 0;
        const quantidade = parseInt(inputQuantidade.value) || 0;

        const total = precoBase * quantidade;

        previewTotal.innerText = "R$ " + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    </script>
</body>
</html>