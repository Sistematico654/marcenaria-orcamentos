<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_servico = $_POST['nome_servico'];
    $categoria = $_POST['categoria'];
    $preco_base = $_POST['preco_base'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO servicos (nome_servico, categoria, preco_base, descricao) 
            VALUES ('$nome_servico', '$categoria', '$preco_base', '$descricao')";

    if (mysqli_query($conn, $sql)) {
        $mensagem = "<div class='alert alert-success' role='alert'>Serviço cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger' role='alert'>Erro ao cadastrar: " . mysqli_error($conn) . "</div>";
    }
}

// Buscar todos os serviços
$servicos = mysqli_query($conn, "SELECT * FROM servicos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Serviços - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        
        <!-- Formulario de Cadastro em Card -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header bg-primary text-white py-3">
                <h3 class="mb-0 fs-4">🛠️ Cadastrar Novo Serviço / Produto</h3>
            </div>
            <div class="card-body p-4">
                <?php echo $mensagem; ?>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="nome_servico" class="form-label fw-bold">Nome do Serviço / Item:</label>
                            <input type="text" id="nome_servico" name="nome_servico" class="form-control" required aria-required="true">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="categoria" class="form-label fw-bold">Categoria:</label>
                            <input type="text" id="categoria" name="categoria" class="form-control" placeholder="Ex: Cozinha, Quarto, Sala">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="preco_base" class="form-label fw-bold">Preço Base (R$):</label>
                            <input type="number" step="0.01" id="preco_base" name="preco_base" class="form-control" placeholder="0.00" required aria-required="true">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label fw-bold">Descrição detalhada:</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar Serviço</button>
                        <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Serviços Cadastrados -->
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h4 class="card-title mb-3">Serviços Cadastrados</h4>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mt-2" aria-label="Tabela de Serviços Cadastrados">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Serviço</th>
                                <th scope="col">Categoria</th>
                                <th scope="col">Preço Base</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($servicos) > 0): ?>
                                <?php while ($s = mysqli_fetch_assoc($servicos)): ?>
                                    <tr>
                                        <th scope="row"><?php echo $s['id']; ?></th>
                                        <td><?php echo htmlspecialchars($s['nome_servico']); ?></td>
                                        <td><?php echo htmlspecialchars($s['categoria']); ?></td>
                                        <td class="fw-bold text-success">
                                            R$ <?php echo number_format($s['preco_base'], 2, ',', '.'); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($s['descricao']); ?></td>
                                        <td class="text-nowrap">
                                            <div class="d-inline-flex gap-1">
                                                <a href="editar_servico.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                                <a href="excluir_servico.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este serviço?');">Excluir</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Nenhum serviço cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>