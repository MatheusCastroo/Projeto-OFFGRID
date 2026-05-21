<?php
$localhost = "localhost";
$username = "root";
$password = "";
$dbname = "calculadora_offgrid";

$conn = new mysqli($localhost, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// echo "Conexão bem-sucedida!";
?>