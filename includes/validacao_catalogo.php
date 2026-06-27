<?php

declare(strict_types=1);

function descricaoIndicaIndisponivel(string $descricao): bool
{
    $descricao = trim($descricao);

    if ($descricao === '') {
        return true;
    }

    return (bool) preg_match('/^indispon[ií]vel$/ui', $descricao);
}

function itemDescricaoTemPreco(string $descricao, bool $possuiPrecoCadastrado): bool
{
    if (descricaoIndicaIndisponivel($descricao)) {
        return false;
    }

    return $possuiPrecoCadastrado;
}

function buscarStatusPrecoPorDescricao(mysqli $conn, string $tabela, string $campoDescricao): array
{
    $tabelasPermitidas = [
        'placa_solar' => 'painel',
        'bateria' => 'bateria_desc',
        'estrutura_solar' => 'estrutura_desc',
    ];

    if (!isset($tabelasPermitidas[$tabela]) || $tabelasPermitidas[$tabela] !== $campoDescricao) {
        return [];
    }

    $sql = sprintf(
        'SELECT %1$s AS descricao, MAX(CASE WHEN preco IS NOT NULL THEN 1 ELSE 0 END) AS tem_preco
         FROM %2$s
         GROUP BY %1$s',
        $campoDescricao,
        $tabela
    );

    $result = $conn->query($sql);
    if (!$result) {
        return [];
    }

    $status = [];

    while ($row = $result->fetch_assoc()) {
        $descricao = (string) ($row['descricao'] ?? '');
        $possuiPreco = ((int) ($row['tem_preco'] ?? 0)) === 1;
        $status[$descricao] = itemDescricaoTemPreco($descricao, $possuiPreco);
    }

    return $status;
}

function carregarStatusPrecosFormulario(mysqli $conn): array
{
    return [
        'placa_solar' => buscarStatusPrecoPorDescricao($conn, 'placa_solar', 'painel'),
        'bateria' => buscarStatusPrecoPorDescricao($conn, 'bateria', 'bateria_desc'),
        'estrutura_solar' => buscarStatusPrecoPorDescricao($conn, 'estrutura_solar', 'estrutura_desc'),
    ];
}

function descricaoTemPrecoNoCatalogo(array $catalogo, string $grupo, string $descricao): bool
{
    return (bool) ($catalogo[$grupo][$descricao] ?? false);
}

function renderOptionComPreco(string $valor, string $rotulo, bool $temPreco): string
{
    $valorEscapado = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    $rotuloEscapado = htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8');
    $flagPreco = $temPreco ? '1' : '0';

    return "<option value=\"{$valorEscapado}\" data-tem-preco=\"{$flagPreco}\">{$rotuloEscapado}</option>";
}
