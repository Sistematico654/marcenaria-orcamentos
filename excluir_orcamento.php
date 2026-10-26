<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM orcamentos WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: listar_orcamentos.php?sucesso=1");
        exit();
    } else {
        echo "Erro ao excluir o orçamento: " . mysqli_error($conn);
    }
} else {
    header("Location: listar_orcamentos.php");
    exit();
}
?>