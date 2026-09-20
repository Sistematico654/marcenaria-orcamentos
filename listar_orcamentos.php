<?php
include 'conexao.php';

// Consulta para trazer orçamentos com dados do cliente (incluindo e-mail e telefone para envio)
$sql = "SELECT o.id, c.nome AS cliente_nome, c.email, c.telefone, s.nome_servico, o.quantidade, o.valor_total, o.data_orcamento, o.status 
        FROM orcamentos o
        INNER JOIN clientes c ON o.cliente_id = c.id
        INNER JOIN servicos s ON o.servico_id = s.id
        ORDER BY o.id DESC";

$resultado = mysqli_query($conn, $sql);

// Indicadores do Status
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
</head>
<body class="bg-light">

    <div class="container mt-5 mb-5">
        
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Status do orçamento atualizado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Painel Resumo de Utilidade do Status -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-success text-white shadow border-0">
                    <div class="card-body">
                        <h5 class="card-title">💰 Receita Aprovada (Em Produção)</h5>
                        <h3 class="fw-bold mb-0">R$ <?php echo number_format($total_aprovado, 2, ',', '.'); ?></h3>
                        <small><?php echo $qtd_aprovado; ?> orçamento(s) aprovado(s)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-warning text-dark shadow border-0">
                    <div class="card-body">
                        <h5 class="card-title">⏳ Orçamentos Pendentes</h5>
                        <h3 class="fw-bold mb-0"><?php echo $qtd_pendente; ?> em negociação</h3>
                        <small>Aguardando resposta do cliente</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0 fs-4">📋 Gestão de Orçamentos</h3>
                <div>
                    <a href="gerar_orcamento.php" class="btn btn-light btn-sm fw-bold">Novo Orçamento</a>
                    <a href="index.php" class="btn btn-outline-light btn-sm ms-1">Voltar ao Menu</a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle" aria-label="Tabela do Histórico de Orçamentos">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($resultado) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                                    <?php 
                                        $num_tel = preg_replace('/\D/', '', $row['telefone'] ?? '');
                                        $msg_whats = "Olá " . urlencode($row['cliente_nome']) . ", segue o seu orçamento para " . urlencode($row['nome_servico']) . " no valor total de R$ " . number_format($row['valor_total'], 2, ',', '.') . ". Qualquer dúvida estamos à disposição!";
                                        $link_whats = "https://api.whatsapp.com/send?phone=55" . $num_tel . "&text=" . $msg_whats;
                                        
                                        $assunto_email = rawurlencode("Orçamento Marcenaria - #" . $row['id']);
                                        $corpo_email = rawurlencode("Olá " . $row['cliente_nome'] . ",\n\nConforme solicitado, segue o orçamento do serviço: " . $row['nome_servico'] . ".\nValor Total: R$ " . number_format($row['valor_total'], 2, ',', '.') . "\n\nFicamos no aguardo da sua aprovação.");
                                        $link_email = "mailto:" . $row['email'] . "?subject=" . $assunto_email . "&body=" . $corpo_email;
                                    ?>
                                    <tr>
                                        <th scope="row">#<?php echo $row['id']; ?></th>
                                        <td><?php echo htmlspecialchars($row['cliente_nome']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nome_servico']); ?></td>
                                        <td class="text-center"><?php echo $row['quantidade']; ?></td>
                                        <td class="text-end fw-bold text-success">
                                            R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo date('d/m/Y', strtotime($row['data_orcamento'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $status = !empty($row['status']) ? $row['status'] : 'Pendente';
                                                if ($status == 'Aprovado') {
                                                    echo '<span class="badge bg-success">Aprovado</span>';
                                                } elseif ($status == 'Rejeitado') {
                                                    echo '<span class="badge bg-danger">Rejeitado</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark">Pendente</span>';
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <?php if ($status != 'Aprovado'): ?>
                                                    <a href="atualizar_status_orcamento.php?id=<?php echo $row['id']; ?>&status=Aprovado" 
                                                       class="btn btn-sm btn-success">Aprovar</a>
                                                <?php endif; ?>

                                                <?php if ($status != 'Rejeitado'): ?>
                                                    <a href="atualizar_status_orcamento.php?id=<?php echo $row['id']; ?>&status=Rejeitado" 
                                                       class="btn btn-sm btn-danger">Rejeitar</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <?php if (!empty($num_tel)): ?>
                                                    <a href="<?php echo $link_whats; ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Enviar orçamento via WhatsApp">
                                                        💬 WhatsApp
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($row['email'])): ?>
                                                    <a href="<?php echo $link_email; ?>" class="btn btn-sm btn-outline-primary" title="Enviar orçamento por E-mail">
                                                        ✉️ E-mail
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Nenhum orçamento registrado até o momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>