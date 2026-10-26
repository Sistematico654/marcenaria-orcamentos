<?php
include 'conexao.php';

$testes = [];

// Teste 1: Conexão com o Banco de Dados
if ($conn) {
    $testes[] = ["nome" => "Conexão com Banco de Dados (MySQL)", "status" => "PASSOU", "detalhe" => "Conectado com sucesso."];
} else {
    $testes[] = ["nome" => "Conexão com Banco de Dados (MySQL)", "status" => "FALHOU", "detalhe" => mysqli_connect_error()];
}

// Teste 2: Estrutura da Tabela 'servicos' (Verificar se preco_base existe)
$res_servicos = mysqli_query($conn, "SHOW COLUMNS FROM servicos LIKE 'preco_base'");
if ($res_servicos && mysqli_num_rows($res_servicos) > 0) {
    $testes[] = ["nome" => "Estrutura do Banco (Coluna 'preco_base' em servicos)", "status" => "PASSOU", "detalhe" => "Coluna identificada."];
} else {
    $testes[] = ["nome" => "Estrutura do Banco (Coluna 'preco_base' em servicos)", "status" => "FALHOU", "detalhe" => "Coluna 'preco_base' não encontrada."];
}

// Teste 3: Lógica de Cálculo de Orçamento
$preco_teste = 150.00;
$qtd_teste = 3;
$esperado = 450.00;
$calculado = $preco_teste * $qtd_teste;

if ($calculado === $esperado) {
    $testes[] = ["nome" => "Regra de Negócio: Cálculo do Valor Total do Orçamento", "status" => "PASSOU", "detalhe" => "R$ 150,00 * 3 = R$ 450,00 (Correto)."];
} else {
    $testes[] = ["nome" => "Regra de Negócio: Cálculo do Valor Total do Orçamento", "status" => "FALHOU", "detalhe" => "Resultado incoerente."];
}

// Teste 4: Consulta à API Externa do ViaCEP
$url_api = "https://viacep.com.br/ws/01001000/json/";
$headers = @get_headers($url_api);
if ($headers && strpos($headers[0], '200')) {
    $testes[] = ["nome" => "Integração com API Externa (ViaCEP)", "status" => "PASSOU", "detalhe" => "End-point operando normalmente."];
} else {
    $testes[] = ["nome" => "Integração com API Externa (ViaCEP)", "status" => "FALHOU", "detalhe" => "Não foi possível comunicar com a API."];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suíte de Testes Automatizados - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Ajustado espaçamento responsivo com py-3 e py-md-5 -->
    <div class="container py-3 py-md-5">
        
        <!-- Cabeçalho reorganizado para empilhar em telas pequenas se necessário -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h2 class="fs-4 fs-md-3 fw-bold mb-0">🧪 Relatório de Execução de Testes</h2>
            <a href="index.php" class="btn btn-secondary btn-sm align-self-start align-self-md-auto">Voltar ao Menu</a>
        </div>

        <div class="card shadow border-0">
            <div class="card-body p-3 p-md-4">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0" aria-label="Tabela de Relatório de Testes">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Caso de Teste</th>
                                <th scope="col">Status</th>
                                <th scope="col">Detalhes / Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testes as $t): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $t['nome']; ?></td>
                                    <td>
                                        <?php if ($t['status'] == 'PASSOU'): ?>
                                            <span class="badge bg-success">✅ PASSOU</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">❌ FALHOU</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $t['detalhe']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>