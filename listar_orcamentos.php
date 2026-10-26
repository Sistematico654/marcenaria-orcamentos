<?php
include 'conexao.php';

// Consulta para trazer orçamentos com dados do cliente (incluindo e-mail e telefone para envio)
$sql = "SELECT o.id, c.nome AS cliente_nome, c.email, c.telefone, s.nome_servico, o.quantidade, o.valor_total, o.data_orcamento, o.status
        FROM orcamentos o
        INNER JOIN clientes c ON o.cliente_id = c.id
        INNER JOIN servicos s ON o.servico_id = s.id
        ORDER BY o.id DESC";

$resultado = mysqli_query($conn, $sql);

// Indicadores do Status para o Painel Superior
$res_aprovados = mysqli_query($conn, "SELECT SUM(valor_total) as total, COUNT(*) as qtd FROM orcamentos WHERE status = 'Aprovado'");
$row_aprovados = mysqli_fetch_assoc($res_aprovados);
$total_aprovado = $row_aprovados['total'] ?? 0;
$qtd_aprovado = $row_aprovados['qtd'] ?? 0;

$res_pendentes = mysqli_query($conn, "SELECT COUNT(*) as qtd FROM orcamentos WHERE status = 'Pendente' OR status IS NULL OR status = ''");
$row_pendentes = mysqli_fetch_assoc($res_pendentes);
$qtd_pendente = $row_pendentes['qtd'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico e Gestão de Orçamentos - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="container py-3 py-md-5">
       
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Ação realizada com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Painel Resumo de Status -->
        <div class="row mb-4">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <div class="card bg-success text-white shadow border-0">
                    <div class="card-body">
                        <h5 class="card-title fs-6 fs-md-5">💰 Receita Aprovada (Pronto para Entrega)</h5>
                        <h3 class="fw-bold mb-0 fs-4 fs-md-3">R$ <?php echo number_format($total_aprovado, 2, ',', '.'); ?></h3>
                        <small><?php echo $qtd_aprovado; ?> orçamento(s) aprovado(s)</small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card bg-warning text-dark shadow border-0">
                    <div class="card-body">
                        <h5 class="card-title fs-6 fs-md-5">⏳ Orçamentos Pendentes</h5>
                        <h3 class="fw-bold mb-0 fs-4 fs-md-3"><?php echo $qtd_pendente; ?> em negociação</h3>
                        <small>Produto em confecção / aguardando</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campo de busca dinâmica -->
        <div class="mb-3">
            <input type="text" id="campoBusca" class="form-control" placeholder="🔍 Digitar para filtrar orçamentos por cliente, serviço ou status...">
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 py-3">
                <h3 class="mb-0 fs-5 fs-md-4 fw-bold">📋 Gestão de Orçamentos</h3>
                <div class="d-flex gap-1">
                    <a href="gerar_orcamento.php" class="btn btn-light btn-sm fw-bold">Novo Orçamento</a>
                    <a href="index.php" class="btn btn-outline-light btn-sm">Voltar ao Menu</a>
                </div>
            </div>
           
            <div class="card-body p-3 p-md-4">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle" id="tabelaOrcamentos" aria-label="Tabela do Histórico de Orçamentos">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col"># ID</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Serviço / Item</th>
                                <th scope="col" class="text-center">Qtd</th>
                                <th scope="col" class="text-end">Valor Total</th>
                                <th scope="col" class="text-center">Data</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Ações de Status</th>
                                <th scope="col" class="text-center">Envio Digital</th>
                                <th scope="col" class="text-center">Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($resultado) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                                    <?php
                                        // Sanitização do telefone e definição do status predefinido
                                        $num_tel = preg_replace('/[^0-9]/', '', (string)$row['telefone']);
                                        if (strlen($num_tel) == 10 || strlen($num_tel) == 11) {
                                            $num_tel = '55' . $num_tel;
                                        }
                                        
                                        $status_atual = !empty($row['status']) ? $row['status'] : 'Pendente';
                                        $nome_cliente = $row['cliente_nome'];
                                        $nome_item    = $row['nome_servico'];
                                        $valor_total  = $row['valor_total'];

                                        // Mesmas mensagens idênticas à página gerar_orcamento.php
                                        if ($status_atual === 'Pendente') {
                                            $texto_status = "Olá {$nome_cliente}! Seu pedido para '{$nome_item}' foi registrado como PENDENTE e já está sendo preparado pela nossa equipe. Valor total: R$ " . number_format($valor_total, 2, ',', '.') . ".";
                                        } elseif ($status_atual === 'Aprovado') {
                                            $texto_status = "Olá {$nome_cliente}! Seu pedido para '{$nome_item}' foi APROVADO e o produto já está PRONTO PARA A ENTREGA/RETIRADA!";
                                        } else { // Negado
                                            $texto_status = "Olá {$nome_cliente}. Infelizmente informamos que o seu pedido para '{$nome_item}' foi NEGADO. Entre em contato conosco para mais esclarecimentos.";
                                        }

                                        $assunto_email = "Atualização de Orçamento - Marcenaria";

                                        $link_whats = "https://api.whatsapp.com/send?phone=" . $num_tel . "&text=" . rawurlencode($texto_status);
                                        $link_email = "mailto:" . $row['email'] . "?subject=" . rawurlencode($assunto_email) . "&body=" . rawurlencode($texto_status);
                                    ?>
                                    <tr>
                                        <th scope="row">#<?php echo $row['id']; ?></th>
                                        <td><?php echo htmlspecialchars($row['cliente_nome']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nome_servico']); ?></td>
                                        <td class="text-center"><?php echo $row['quantidade']; ?></td>
                                        <td class="text-end fw-bold text-success text-nowrap">
                                            R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <?php echo date('d/m/Y', strtotime($row['data_orcamento'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                if ($status_atual == 'Aprovado') {
                                                    echo '<span class="badge bg-success">Aprovado</span>';
                                                } elseif ($status_atual == 'Negado') {
                                                    echo '<span class="badge bg-danger">Negado</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark">Pendente</span>';
                                                }
                                            ?>
                                        </td>
                                        <!-- Botões de Alteração de Status -->
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <?php if ($status_atual != 'Pendente'): ?>
                                                    <a href="atualizar_status_orcamento.php?id=<?php echo $row['id']; ?>&status=Pendente"
                                                       class="btn btn-sm btn-outline-warning text-dark" title="Mudar para Pendente">Pend.</a>
                                                <?php endif; ?>

                                                <?php if ($status_atual != 'Aprovado'): ?>
                                                    <a href="atualizar_status_orcamento.php?id=<?php echo $row['id']; ?>&status=Aprovado"
                                                       class="btn btn-sm btn-success" title="Mudar para Aprovado">Aprovar</a>
                                                <?php endif; ?>

                                                <?php if ($status_atual != 'Negado'): ?>
                                                    <a href="atualizar_status_orcamento.php?id=<?php echo $row['id']; ?>&status=Negado"
                                                       class="btn btn-sm btn-danger" title="Mudar para Negado">Negar</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <!-- Botões de Envio Digital -->
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <?php if (!empty($num_tel)): ?>
                                                    <a href="<?php echo $link_whats; ?>" target="_blank" class="btn btn-sm btn-success" title="Enviar orçamento via WhatsApp">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($row['email'])): ?>
                                                    <a href="<?php echo $link_email; ?>" class="btn btn-sm btn-primary" title="Enviar orçamento por E-mail">
                                                        <i class="bi bi-envelope"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <!-- Botão de Exclusão -->
                                        <td class="text-center">
                                            <a href="excluir_orcamento.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Tem certeza que deseja excluir o orçamento #<?php echo $row['id']; ?>?');" 
                                               title="Excluir Orçamento">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Nenhum orçamento registrado até o momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script de Busca Dinâmica em Tempo Real -->
    <script>
    document.getElementById('campoBusca').addEventListener('keyup', function() {
        let termo = this.value.toLowerCase();
        let linhas = document.querySelectorAll('#tabelaOrcamentos tbody tr');

        linhas.forEach(linha => {
            let textoLinha = linha.textContent.toLowerCase();
            if (textoLinha.includes(termo)) {
                linha.style.display = '';
            } else {
                linha.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>