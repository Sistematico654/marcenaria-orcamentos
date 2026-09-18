<?php
include('conexao.php');

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email    = $_POST['email'];
    $endereco = $_POST['endereco'];

    $sql = "INSERT INTO clientes (nome, telefone, email, endereco) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $telefone, $email, $endereco);

    if ($stmt->execute()) {
        $mensagem = "<div class='alert alert-success'>Cliente cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . $conn->error . "</div>";
    }
}

$result = $conn->query("SELECT * FROM clientes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn btn-sm btn-outline-secondary mb-3">← Voltar ao Menu Principal</a>

        <?= $mensagem; ?>

        <div class="card p-4 mb-4">
            <h3 class="card-title mb-3">Cadastrar Novo Cliente</h3>
            <form action="cadastrar_cliente.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome:</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telefone / WhatsApp:</label>
                        <input type="text" name="telefone" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail:</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço:</label>
                    <textarea name="endereco" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Salvar Cliente</button>
            </form>
        </div>

        <div class="table-container">
            <h4 class="mb-3">Clientes Cadastrados</h4>
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Endereço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['nome']); ?></td>
                        <td><?= htmlspecialchars($row['telefone']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['endereco']); ?></td>
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