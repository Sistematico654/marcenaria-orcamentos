<?php
include 'conexao.php';

$mensagem = "";
$dados_notificacao = null; // Armazena dados para os botões de envio

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $servico_id = $_POST['servico_id'];
    $quantidade = $_POST['quantidade'];
    $status     = $_POST['status']; // Captura o status selecionado

    // Buscar o valor unitário usando a coluna preco_base
    $query_servico = "SELECT nome_servico, preco_base FROM servicos WHERE id = '$servico_id'";
    $res_servico = mysqli_query($conn, $query_servico);
    $servico = mysqli_fetch_assoc($res_servico);

    // Buscar dados do cliente para contato
    $query_cliente = "SELECT nome, telefone, email FROM clientes WHERE id = '$cliente_id'";
    $res_cliente = mysqli_query($conn, $query_cliente);
    $cliente = mysqli_fetch_assoc($res_cliente);

    if ($servico && $cliente) {
        $valor_unitario = $servico['preco_base'];
        $valor_total = $valor_unitario * $quantidade;
        $data_orcamento = date('Y-m-d');

        // Insere o orçamento gravando o status escolhido
        $sql = "INSERT INTO orcamentos (cliente_id, servico_id, quantidade, valor_total, data_orcamento, status)
                VALUES ('$cliente_id', '$servico_id', '$quantidade', '$valor_total', '$data_orcamento', '$status')";

        if (mysqli_query($conn, $sql)) {
            $mensagem = "<div class='alert alert-success' role='alert'>Orçamento gerado com sucesso! Valor Total: R$ " . number_format($valor_total, 2, ',', '.') . "</div>";
            
            // Tratamento das mensagens personalizadas por status
            $nome_cliente = $cliente['nome'];
            $nome_item    = $servico['nome_servico'];
            $texto_status = "";

            if ($status === 'Pendente') {
                $texto_status = "Olá {$nome_cliente}! Seu pedido para '{$nome_item}' foi registrado como PENDENTE e já está sendo preparado pela nossa equipe. Valor total: R$ " . number_format($valor_total, 2, ',', '.') . ".";
            } elseif ($status === 'Aprovado') {
                $texto_status = "Olá {$nome_cliente}! Seu pedido para '{$nome_item}' foi APROVADO e o produto já está PRONTO PARA A ENTREGA/RETIRADA!";
            } elseif ($status === 'Negado') {
                $texto_status = "Olá {$nome_cliente}. Infelizmente informamos que o seu pedido para '{$nome_item}' foi NEGADO. Entre em contato conosco para mais esclarecimentos.";
            }

            // Sanitização do telefone (remove tudo o que não for dígito numérico com segurança)
            $telefone_limpo = preg_replace('/[^0-9]/', '', (string)$cliente['telefone']);
            if (strlen($telefone_limpo) == 10 || strlen($telefone_limpo) == 11) {
                $telefone_limpo = '55' . $telefone_limpo; // Adiciona DDI Brasil se necessário
            }

            $dados_notificacao = [
                'telefone' => $telefone_limpo,
                'email'    => $cliente['email'],
                'mensagem' => $texto_status,
                'status'   => $status
            ];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-3 py-md-5">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white py-3">
                <h3 class="mb-0 fs-5 fs-md-3 fw-bold">📄 Gerar Novo Orçamento</h3>
            </div>
            <div class="card-body p-3 p-md-4">
                <?php echo $mensagem; ?>

                <!-- Exibe os botões de envio imediato após salvar o orçamento -->
                <?php if ($dados_notificacao): ?>
                    <div class="card mb-4 border-info bg-light">
                        <div class="card-body">
                            <h5 class="card-title text-info-emphasis fw-bold">📲 Notificar Cliente (Status: <?php echo $dados_notificacao['status']; ?>)</h5>
                            <p class="card-text text-muted mb-3"><em>"<?php echo htmlspecialchars($dados_notificacao['mensagem']); ?>"</em></p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (!empty($dados_notificacao['telefone'])): ?>
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo $dados_notificacao['telefone']; ?>&text=<?php echo rawurlencode($dados_notificacao['mensagem']); ?>" 
                                       target="_blank" class="btn btn-success">
                                        <i class="bi bi-whatsapp"></i> Enviar por WhatsApp
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($dados_notificacao['email'])): ?>
                                    <a href="mailto:<?php echo $dados_notificacao['email']; ?>?subject=<?php echo rawurlencode("Atualização de Orçamento - Marcenaria"); ?>&body=<?php echo rawurlencode($dados_notificacao['mensagem']); ?>" 
                                       target="_blank" class="btn btn-primary">
                                        <i class="bi bi-envelope"></i> Enviar por E-mail
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

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

                    <!-- Campo de Seleção de Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Inicial do Orçamento:</label>
                        <select name="status" class="form-select" required>
                            <option value="Pendente" selected>Pendente (Produto em confecção)</option>
                            <option value="Aprovado">Aprovado (Pronto para entrega)</option>
                            <option value="Negado">Negado (Pedido cancelado/recusado)</option>
                        </select>
                    </div>

                    <!-- Exibição prévia do valor total em tempo real -->
                    <div class="alert alert-info fw-bold fs-6 fs-md-5">
                        Valor Total Estimado: <span id="valor_total_preview">R$ 0,00</span>
                    </div>

                    <div class="d-grid d-md-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar e Registrar Orçamento</button>
                        <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script JavaScript / Cálculo Dinâmico -->
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