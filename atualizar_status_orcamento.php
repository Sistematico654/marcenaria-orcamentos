<?php
include 'conexao.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    // Atualiza o status do orçamento no banco de dados
    $sql = "UPDATE orcamentos SET status = '$status' WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: listar_orcamentos.php?sucesso=1");
        exit();
    } else {
        echo "Erro ao atualizar status: " . mysqli_error($conn);
    }
} else {
    header("Location: listar_orcamentos.php");
    exit();
}
?>