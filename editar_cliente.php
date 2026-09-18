<?php
include('conexao.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: cadastrar_cliente.php");
    exit();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email    = $_POST['email'];
    $endereco = $_POST['endereco'];

    $stmt = $conn->prepare("UPDATE clientes SET nome = ?, telefone = ?, email = ?, endereco = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nome, $telefone, $email, $endereco, $id);

    if ($stmt->execute()) {
        header("Location: cadastrar_cliente.php");
        exit();
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao atualizar: " . $conn->error . "</div>";
    }
}

$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="cadastrar_cliente.php" class="btn btn-sm btn-outline-secondary mb-3">← Voltar</a>
        <?= $mensagem; ?>
        <div class="card p-4">
            <h3 class="card-title mb-3">Editar Cliente #<?= $cliente['id']; ?></h3>
            <form action="editar_cliente.php?id=<?= $id; ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome:</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telefone / WhatsApp:</label>
                        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($cliente['telefone']); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail:</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço:</label>
                    <textarea name="endereco" class="form-control" rows="2"><?= htmlspecialchars($cliente['endereco']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>