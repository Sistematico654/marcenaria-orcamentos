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
        $mensagem = "<div class='alert alert-danger' role='alert'>Erro ao atualizar: " . $conn->error . "</div>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Ajustado espaçamento responsivo com py-3 e py-md-5 -->
    <div class="container py-3 py-md-5">
        <div class="mb-3">
            <a href="cadastrar_cliente.php" class="btn btn-sm btn-outline-secondary">← Voltar</a>
        </div>
        
        <?= $mensagem; ?>

        <div class="card shadow border-0">
            <div class="card-body p-3 p-md-4">
                <!-- Título responsivo para celulares e PCs -->
                <h3 class="card-title mb-4 fs-5 fs-md-3 fw-bold">✏️ Editar Cliente #<?= $cliente['id']; ?></h3>
                
                <form action="editar_cliente.php?id=<?= $id; ?>" method="POST">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label fw-bold">Nome:</label>
                            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome']); ?>" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label fw-bold">Telefone / WhatsApp:</label>
                            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($cliente['telefone']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail:</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Endereço:</label>
                        <textarea name="endereco" class="form-control" rows="3"><?= htmlspecialchars($cliente['endereco']); ?></textarea>
                    </div>

                    <!-- Botão responsivo (ocupa 100% da largura no mobile) -->
                    <div class="d-grid d-md-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <a href="cadastrar_cliente.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>