<?php
include 'conexao.php';

if (isset($_GET['id']) && isset($_GET['novo_status'])) {
    $id = $_GET['id'];
    $novo_status = $_GET['novo_status'];

    $sql = "UPDATE orcamentos SET status = '$novo_status' WHERE id = '$id'";
    mysqli_query($conn, $sql);
}

header("Location: listar_orcamentos.php");
exit();
?>