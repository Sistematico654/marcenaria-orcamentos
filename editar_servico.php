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
        $mensagem = "<div class='alert alert-danger' role='alert'>Erro ao atualizar: " . $conn->error . "</div>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviço - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Ajustado py-3 e py-md-5 para otimizar o espaço no celular -->
    <div class="container py-3 py-md-5">
        <div class="mb-3">
            <a href="cadastrar_servico.php" class="btn btn-sm btn-outline-secondary">← Voltar</a>
        </div>

        <?= $mensagem; ?>

        <div class="card shadow border-0">
            <div class="card-body p-3 p-md-4">
                <!-- Título responsivo para celulares e PCs -->
                <h3 class="card-title mb-4 fs-5 fs-md-3 fw-bold">🛠️ Editar Serviço #<?= $servico['id']; ?></h3>
                
                <form action="editar_servico.php?id=<?= $id; ?>" method="POST">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label fw-bold">Nome do Serviço / Item:</label>
                            <input type="text" name="nome_servico" class="form-control" value="<?= htmlspecialchars($servico['nome_servico']); ?>" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label fw-bold">Categoria:</label>
                            <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($servico['categoria']); ?>" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label fw-bold">Preço Base (R$):</label>
                            <input type="number" step="0.01" name="preco_base" class="form-control" value="<?= $servico['preco_base']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição detalhada:</label>
                        <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($servico['descricao']); ?></textarea>
                    </div>

                    <!-- Botão responsivo (ocupa 100% da largura no mobile) -->
                    <div class="d-grid d-md-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        <a href="cadastrar_servico.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>