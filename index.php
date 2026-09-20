<?php
include 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Marcenaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .visually-hidden-focusable:not(:focus):not(:focus-within) {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Atalho de acessibilidade para saltar diretamente para o conteúdo principal -->
    <a class="visually-hidden-focusable btn btn-primary m-2" href="#conteudo-principal">Ir para o conteúdo principal</a>

    <!-- Cabeçalho Principal (HTML5 Semântico) -->
    <header class="bg-dark text-white py-4 shadow-sm" role="banner">
        <div class="container text-center">
            <h1 class="h2 mb-0">🪚 Sistema de Gestão de Marcenaria</h1>
            <p class="mb-0 text-white-50">Controle de Clientes, Serviços e Orçamentos</p>
        </div>
    </header>

    <!-- Menu de Navegação -->
    <nav class="bg-secondary py-2" role="navigation" aria-label="Navegação Principal">
        <div class="container text-center">
            <p class="text-light small mb-2">Navegue pelas opções abaixo utilizando a tecla TAB</p>
            <div>
                <a href="teste_sistema.php" class="btn btn-sm btn-outline-warning text-white fw-bold" aria-label="Executar testes automatizados do sistema">
                    🧪 Rodar Testes do Sistema
                </a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main id="conteudo-principal" class="container my-5" role="main" tabindex="-1">
        <section aria-labelledby="titulo-painel">
            <h2 id="titulo-painel" class="h4 mb-4 text-secondary border-bottom pb-2">Painel de Controle</h2>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="h5 card-title">👤 Clientes</h3>
                                <p class="card-text text-muted">Registre novos clientes e faça a gestão das suas informações de contato e endereço.</p>
                            </div>
                            <a href="cadastrar_cliente.php" class="btn btn-primary mt-3" aria-label="Aceder ao formulário de Gestão de Clientes">Gerir Clientes</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="h5 card-title">🛠️ Serviços</h3>
                                <p class="card-text text-muted">Cadastre a lista de serviços oferecidos pela marcenaria e os respectivos preços base.</p>
                            </div>
                            <a href="cadastrar_servico.php" class="btn btn-primary mt-3" aria-label="Aceder à página de Gestão de Serviços">Gerir Serviços</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="h5 card-title">📄 Orçamentos</h3>
                                <p class="card-text text-muted">Gere orçamentos personalizados com cálculo automático em tempo real.</p>
                            </div>
                            <a href="gerar_orcamento.php" class="btn btn-success mt-3" aria-label="Aceder à página para Gerar Novo Orçamento">Gerar Orçamento</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <h3 class="h5 card-title">📋 Histórico</h3>
                                <p class="card-text text-muted">Consulte os orçamentos emitidos e altere os status.</p>
                            </div>
                            <a href="listar_orcamentos.php" class="btn btn-info text-white mt-3" aria-label="Ver Histórico de Orçamentos">Histórico de Orçamentos</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Rodapé -->
    <footer class="bg-dark text-white text-center py-3 mt-auto" role="contentinfo">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> Projeto Integrador - Univesp | Engenharia de Computação</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>