<?php
include 'conexao.php';

// Consulta para buscar orçamentos unindo os dados do cliente e do serviço
$sql = "SELECT o.id, c.nome AS cliente_nome, s.nome_servico, o.quantidade, o.valor_total, o.data_orcamento, o.status 
        FROM orcamentos o
        INNER JOIN clientes c ON o.cliente_id = c.id
        INNER JOIN servicos s ON o.servico_id = s.id
        ORDER BY o.id DESC";

$resultado = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Orçamentos - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>📋 Orçamentos Registrados</h2>
            <div>
                <a href="gerar_orcamento.php" class="btn btn-success">➕ Novo Orçamento</a>
                <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>Cliente</th>
                            <th>Serviço / Item</th>
                            <th>Qtd</th>
                            <th>Valor Total</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while ($o = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><?php echo $o['id']; ?></td>
                                    <td><?php echo htmlspecialchars($o['cliente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($o['nome_servico']); ?></td>
                                    <td><?php echo $o['quantidade']; ?></td>
                                    <td>R$ <?php echo number_format($o['valor_total'], 2, ',', '.'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($o['data_orcamento'])); ?></td>
                                    <td><span class="badge bg-warning text-dark"><?php echo $o['status']; ?></span></td>
                                    <td><a href="atualizar_status_orcamento.php?id=<?php echo $o['id']; ?>&novo_status=Aprovado" class="btn btn-sm btn-outline-success">Aprovar</a>
                                        <a href="atualizar_status_orcamento.php?id=<?php echo $o['id']; ?>&novo_status=Rejeitado" class="btn btn-sm btn-outline-danger">Rejeitar</a>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum orçamento encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>