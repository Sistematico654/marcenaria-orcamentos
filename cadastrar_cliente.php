<?php
include 'conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome =$_POST['nome'];
    $telefone =$_POST['telefone'];
    $email =$_POST['email'];
    $cep =$_POST['cep'];
    $endereco =$_POST['endereco'];

    // Salva o cliente incluindo o CEP e Endereço
    $sql = "INSERT INTO clientes (nome, telefone, email, endereco) VALUES ('$nome', '$telefone', '$email', '$cep -$endereco')";

    if (mysqli_query($conn, $sql)) {$mensagem = "<div class='alert alert-success'>Cliente cadastrado com sucesso!</div>";
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
    <title>Cadastrar Cliente - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">👤 Cadastrar Novo Cliente</h3>
            </div>
            <div class="card-body">
                <?php echo $mensagem; ?>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo:</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telefone:</label>
                            <input type="text" name="telefone" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">E-mail:</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>

                    <!-- Campos com Integração ViaCEP -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CEP (Apenas números):</label>
                            <input type="text" id="cep" name="cep" class="form-control" maxlength="8" placeholder="00000000" onblur="buscarCEP()">
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Endereço Completo:</label>
                            <input type="text" id="endereco" name="endereco" class="form-control" placeholder="Rua, Bairro, Cidade - UF" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Salvar Cliente</button>
                    <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                </form>
            </div>
        </div>

        <!-- Tabela de Clientes Cadastrados -->
        <div class="card shadow">
            <div class="card-body">
                <h4>Clientes Cadastrados</h4>
                <table class="table table-striped align-middle mt-3">
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script JavaScript / Consumo de API ViaCEP -->
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