<?php
include('conexao.php');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: cadastrar_servico.php");
    exit();
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome      = $_POST['nome_servico'];
    $categoria = $_POST['categoria'];
    $preco     = $_POST['preco_base'];
    $descricao = $_POST['descricao'];

    $stmt = $conn->prepare("UPDATE servicos SET nome_servico = ?, categoria = ?, preco_base = ?, descricao = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $nome, $categoria, $preco, $descricao, $id);

    if ($stmt->execute()) {
        header("Location: cadastrar_servico.php");
        exit();
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao atualizar: " . $conn->error . "</div>";
    }
}

$stmt = $conn->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$servico = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="cadastrar_servico.php" class="btn btn-sm btn-outline-secondary mb-3">← Voltar</a>
        <?= $mensagem; ?>
        <div class="card p-4">
            <h3 class="card-title mb-3">Editar Serviço #<?= $servico['id']; ?></h3>
            <form action="editar_servico.php?id=<?= $id; ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nome do Serviço/Item:</label>
                        <input type="text" name="nome_servico" class="form-control" value="<?= htmlspecialchars($servico['nome_servico']); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Categoria:</label>
                        <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($servico['categoria']); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Preço Base (R$):</label>
                        <input type="number" step="0.01" name="preco_base" class="form-control" value="<?= $servico['preco_base']; ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição detalhada:</label>
                    <textarea name="descricao" class="form-control" rows="2"><?= htmlspecialchars($servico['descricao']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>