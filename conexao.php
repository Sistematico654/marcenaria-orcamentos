<?php
// Lê as variáveis de ambiente que o Railway injeta automaticamente
$host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
$user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
$db   = getenv('MYSQL_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'marcenaria_db';
$port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';

$conn = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
?>