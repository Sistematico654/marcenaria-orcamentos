// Aguarda o carregamento do DOM da página
document.addEventListener('DOMContentLoaded', function () {

    // 1. Confirmação ao clicar nos botões de Mudar Status
    const linksStatus = document.querySelectorAll('a[href*="atualizar_status_orcamento.php"]');
    
    linksStatus.forEach(function (link) {
        link.addEventListener('click', function (e) {
            const URL = new URL(link.href);
            const status = URL.searchParams.get('status');
            
            let mensagem = "Deseja realmente alterar o status deste orçamento?";
            if (status === 'Aprovado') {
                mensagem = "Deseja alterar o status para APROVADO? O pedido entrará em produção.";
            } else if (status === 'Rejeitado') {
                mensagem = "Deseja realmente REJEITAR/CANCELAR este orçamento?";
            }

            if (!confirm(mensagem)) {
                e.preventDefault(); // Cancela o clique se o usuário escolher "Cancelar"
            }
        });
    });

    // 2. Filtro em tempo real para a Tabela de Orçamentos
    const campoBusca = document.getElementById('campoBusca');
    if (campoBusca) {
        campoBusca.addEventListener('keyup', function () {
            const termo = campoBusca.value.toLowerCase();
            const linhas = document.querySelectorAll('tbody tr');

            linhas.forEach(function (linha) {
                const textoLinha = linha.textContent.toLowerCase();
                if (textoLinha.includes(termo)) {
                    linha.style.display = '';
                } else {
                    linha.style.display = 'none';
                }
            });
        });
    }
});