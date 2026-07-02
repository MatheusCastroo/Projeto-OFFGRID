<?php

declare(strict_types=1);

/**
 * Gera migracao_estoque.sql a partir de dados/estoque.csv
 *
 * Uso: php scripts/gerar_migracao_estoque.php
 */

$baseDir = dirname(__DIR__);
$csvPath = $baseDir . '/dados/estoque.csv';
$outputPath = $baseDir . '/migracao_estoque.sql';

$mapaTabelas = [
    'bateria' => [13917, 14121, 14372, 27548, 27612, 27919, 28887, 31457, 31570],
    'placa_solar' => [
        18450, 20745, 20746, 22444, 22473, 22479, 23069, 23236, 23237, 23904, 24248,
        24533, 24534, 25220, 25896, 26035, 26036, 26386, 26387, 26863, 26890, 27193,
        27233, 27254, 28138, 28233, 28824, 28825, 28864, 29546, 29547, 30214, 30448,
        30510, 30520, 30606, 30720, 31169, 31228,
    ],
    'controlador_carga' => [
        22776, 22777, 22778, 22779, 22780, 22781, 22782, 22783,
        22785, 22786, 22790, 22791, 22792, 22793,
    ],
    'disjuntor' => [
        21341, 21342, 21343, 21344, 21345, 21346, 21347, 21348,
        26935, 26936, 26937, 26938, 26939, 26940, 26941, 26942,
    ],
    'estrutura_solar' => [0, 21749, 28521],
    'inversor' => [
        22768, 22769, 22770, 22771, 25378, 25379, 25380, 25381, 25382,
        25413, 25414, 25415, 29763, 31377,
    ],
];

$skuParaTabela = [];
foreach ($mapaTabelas as $tabela => $skus) {
    foreach ($skus as $sku) {
        $skuParaTabela[$sku] = $tabela;
    }
}

if (!is_readable($csvPath)) {
    fwrite(STDERR, "Arquivo não encontrado: {$csvPath}\n");
    exit(1);
}

$estoquePorSku = [];
$handle = fopen($csvPath, 'rb');
if ($handle === false) {
    fwrite(STDERR, "Não foi possível abrir: {$csvPath}\n");
    exit(1);
}

$linha = 0;
while (($row = fgetcsv($handle)) !== false) {
    $linha++;
    if ($linha === 1 && isset($row[0]) && strcasecmp(trim((string) $row[0]), 'sku') === 0) {
        continue;
    }

    if (count($row) < 2) {
        continue;
    }

    $sku = (int) trim((string) $row[0]);
    $estoque = str_replace(',', '.', trim((string) $row[1]));
    $estoquePorSku[$sku] = number_format((float) $estoque, 4, '.', '');
}
fclose($handle);

$sql = [];
$sql[] = '-- Migracao: coluna estoque + carga completa a partir de dados/estoque.csv';
$sql[] = '-- Gerado em: ' . date('d/m/Y H:i:s');
$sql[] = 'USE `calculadora_offgrid`;';
$sql[] = '';

$tabelas = array_keys($mapaTabelas);
foreach ($tabelas as $tabela) {
    $sql[] = "ALTER TABLE `{$tabela}` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;";
}
$sql[] = '';

$skusSemTabela = [];
$updatesPorTabela = array_fill_keys($tabelas, []);

foreach ($estoquePorSku as $sku => $estoque) {
    $tabela = $skuParaTabela[$sku] ?? null;
    if ($tabela === null) {
        $skusSemTabela[] = $sku;
        continue;
    }

    $updatesPorTabela[$tabela][] = "UPDATE `{$tabela}` SET `estoque` = {$estoque} WHERE `sku` = {$sku};";
}

foreach ($tabelas as $tabela) {
    if ($updatesPorTabela[$tabela] === []) {
        continue;
    }

    $sql[] = '-- ' . strtoupper(str_replace('_', ' ', $tabela));
    array_push($sql, ...$updatesPorTabela[$tabela]);
    $sql[] = '';
}

foreach ($mapaTabelas as $tabela => $skus) {
    $faltantes = array_filter($skus, static fn (int $sku): bool => !array_key_exists($sku, $estoquePorSku));
    if ($faltantes === []) {
        continue;
    }

    $sql[] = '-- SKUs do banco sem linha no CSV (' . $tabela . '): estoque zerado';
    foreach ($faltantes as $sku) {
        $sql[] = "UPDATE `{$tabela}` SET `estoque` = 0.0000 WHERE `sku` = {$sku};";
    }
    $sql[] = '';
}

if ($skusSemTabela !== []) {
    $sql[] = '-- SKUs presentes no CSV, mas ausentes no banco:';
    foreach ($skusSemTabela as $sku) {
        $sql[] = '-- SKU ' . $sku . ' ignorado';
    }
    $sql[] = '';
}

file_put_contents($outputPath, implode(PHP_EOL, $sql));

echo 'Arquivo gerado: ' . $outputPath . PHP_EOL;
echo 'Registros no CSV: ' . count($estoquePorSku) . PHP_EOL;
