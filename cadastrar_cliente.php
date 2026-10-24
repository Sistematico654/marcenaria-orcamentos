<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];
    $endereco_informado = $_POST['endereco'];

    $endereco_completo = "$cep - $endereco_informado";

    $stmt = mysqli_prepare($conn, "INSERT INTO clientes (nome, telefone, email, endereco) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $nome, $telefone, $email, $endereco_completo);

    if (mysqli_stmt_execute($stmt)) {
        $mensagem = "<div class='alert alert-success' role='alert'>Cliente cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger' role='alert'>Erro ao cadastrar: " . mysqli_error($conn) . "</div>";
    }
    mysqli_stmt_close($stmt);
}

$clientes = mysqli_query($conn, "SELECT * FROM clientes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-3 py-md-5">
        
        <!-- Card de Cadastro -->
        <div class="card shadow mb-4 border-0">
            <!-- Ajustado o padding (py-3) e tamanho da fonte responsiva no título -->
            <div class="card-header bg-primary text-white py-3">
                <h3 class="mb-0 fs-5 fs-md-3 fw-bold">👤 Cadastrar Novo Cliente</h3>
            </div>
            <div class="card-body p-3 p-md-4">
                <?php echo $mensagem; ?>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label fw-bold">Nome Completo:</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label fw-bold">Telefone:</label>
                            <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label fw-bold">E-mail:</label>
                            <input type="email" name="email" class="form-control" placeholder="exemplo@email.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label fw-bold">CEP (Apenas números):</label>
                            <input type="text" id="cep" name="cep" class="form-control" maxlength="8" placeholder="00000000" onblur="buscarCEP()">
                        </div>
                        <div class="col-12 col-md-9 mb-3">
                            <label class="form-label fw-bold">Endereço Completo:</label>
                            <input type="text" id="endereco" name="endereco" class="form-control" placeholder="Rua, Bairro, Cidade - UF" required>
                        </div>
                    </div>

                    <div class="d-grid d-md-flex gap-2">
                        <button type="submit" class="btn btn-success">Salvar Cliente</button>
                        <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Clientes Cadastrados -->
        <div class="card shadow border-0">
            <div class="card-body p-3 p-md-4">
                <!-- Título da tabela ajustado para mobile com fs-5 -->
                <h4 class="card-title mb-3 fs-5 fw-bold">Clientes Cadastrados</h4>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mt-2" aria-label="Tabela de Clientes Cadastrados">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Telefone</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Endereço</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($clientes) > 0): ?>
                                <?php while ($c = mysqli_fetch_assoc($clientes)): ?>
                                    <tr>
                                        <th scope="row"><?php echo $c['id']; ?></th>
                                        <td><?php echo htmlspecialchars($c['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($c['telefone']); ?></td>
                                        <td><?php echo htmlspecialchars($c['email']); ?></td>
                                        <td><?php echo htmlspecialchars($c['endereco']); ?></td>
                                        <td class="text-nowrap">
                                            <div class="d-inline-flex gap-1">
                                                <a href="editar_cliente.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                                <a href="excluir_cliente.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este cliente?');">Excluir</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Nenhum cliente cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function buscarCEP() {
        let cep = document.getElementById('cep').value.replace(/\D/g, '');

        if (cep.length === 8) {
            document.getElementById('endereco').value = "Buscando endereço...";

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        document.getElementById('endereco').value = `${dados.logradouro}, ${dados.bairro} - ${dados.localidade}/${dados.uf}`;
                    } else {
                        alert("CEP não encontrado!");
                        document.getElementById('endereco').value = "";
                    }
                })
                .catch(erro => {
                    alert("Erro ao buscar o CEP!");
                    document.getElementById('endereco').value = "";
                });
        }
    }
    </script>
</body>
</html>