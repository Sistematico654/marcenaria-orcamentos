<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];
    $endereco = $_POST['endereco'];

    // Salva o cliente incluindo o CEP e Endereço exatamente como no seu código original
    $sql = "INSERT INTO clientes (nome, telefone, email, endereco) VALUES ('$nome', '$telefone', '$email', '$cep - $endereco')";

    if (mysqli_query($conn, $sql)) {
        $mensagem = "<div class='alert alert-success'>Cliente cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger'>Erro ao cadastrar: " . mysqli_error($conn) . "</div>";
    }
}

// Buscar todos os clientes
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
    <!-- Ajustado py-3 e py-md-5 para otimizar o espaço no celular -->
    <div class="container py-3 py-md-5">
        <div class="card shadow mb-4 border-0">
            <!-- Título com tamanho responsivo (fs-5 no celular, fs-3 no PC) para não ficar pequeno -->
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
                            <input type="text" name="telefone" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label fw-bold">E-mail:</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>

                    <!-- Campos com Integração ViaCEP -->
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

                    <!-- Botão com d-grid para ocupar a largura correta no celular -->
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
                <h4 class="card-title mb-3 fs-5 fw-bold">Clientes Cadastrados</h4>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mt-3" aria-label="Tabela de Clientes Cadastrados">
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
                            <?php if (mysqli_num_rows($clientes) > 0): ?>
                                <?php while ($c = mysqli_fetch_assoc($clientes)): ?>
                                    <tr>
                                        <td><?php echo $c['id']; ?></td>
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

    <!-- Script JavaScript / Consumo de API ViaCEP (Mantido exatamente como o seu original) -->
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