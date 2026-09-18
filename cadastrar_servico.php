<?php
include('conexao.php');

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome      = $_POST['nome_servico'];
    $categoria = $_POST['categoria'];
    $preco     = $_POST['preco_base'];
    $descricao = $_POST['descricao'];

    $sql = "INSERT INTO servicos (nome_servico, categoria, preco_base, descricao) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $nome, $categoria, $preco, $descricao);

    if ($stmt->execute()) {
        $mensagem = "<div class='alert alert-success'>Serviço cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . $conn->error . "</div>";
    }
}

$result = $conn->query("SELECT * FROM servicos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-sm btn-outline-secondary mb-3">← Voltar ao Menu Principal</a>

        <?= $mensagem; ?>

        <div class="card p-4 mb-4">
            <h3 class="card-title mb-3">Cadastrar Novo Serviço / Produto</h3>
            <form action="cadastrar_servico.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome do Serviço/Item:</label>
                        <input type="text" name="nome_servico" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Categoria:</label>
                        <input type="text" name="categoria" class="form-control" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Preço Base (R$):</label>
                        <input type="number" step="0.01" name="preco_base" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição detalhada:</label>
                    <textarea name="descricao" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Salvar Serviço</button>
            </form>
        </div>

        <div class="table-container">
            <h4 class="mb-3">Serviços Cadastrados</h4>
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Serviço</th>
                        <th>Categoria</th>
                        <th>Preço Base (R$)</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nome_servico']); ?></td>
                        <td><?= htmlspecialchars($row['categoria']); ?></td>
                        <td>R$ <?= number_format($row['preco_base'], 2, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($row['descricao']); ?></td>
                        <td class="text-nowrap">
                            <div class="d-inline-flex gap-1">
                                <a href="editar_cliente.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="excluir_cliente.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este cliente?');">Excluir</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>