<?php

session_start();

require_once __DIR__ . '/includes/gerador_relatorio.php';

if (empty($_SESSION['dimensionamento_export'])) {
    header('Location: index.php');
    exit;
}

$formato = $_GET['formato'] ?? '';
$dados = $_SESSION['dimensionamento_export'];

if ($formato === 'excel') {
    gerarExcel($dados);
}

if ($formato === 'pdf') {
    gerarPdf($dados);
}

header('Location: index.php');
exit;
