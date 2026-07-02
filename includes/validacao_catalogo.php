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

function colunaExiste(mysqli $conn, string $tabela, string $coluna): bool
{
    $sql = 'SELECT COUNT(*) AS total
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('ss', $tabela, $coluna);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return ((int) ($row['total'] ?? 0)) > 0;
}

function buscarStatusCatalogoPorDescricao(mysqli $conn, string $tabela, string $campoDescricao, bool $possuiColunaEstoque): array
{
    $tabelasPermitidas = [
        'placa_solar' => 'painel',
        'bateria' => 'bateria_desc',
        'estrutura_solar' => 'estrutura_desc',
    ];

    if (!isset($tabelasPermitidas[$tabela]) || $tabelasPermitidas[$tabela] !== $campoDescricao) {
        return [];
    }

    $sqlEstoque = $possuiColunaEstoque
        ? 'CASE
                WHEN SUM(CASE WHEN estoque IS NULL THEN 1 ELSE 0 END) = COUNT(*) THEN 1
                WHEN MAX(COALESCE(estoque, 0)) > 0 THEN 1
                ELSE 0
           END AS tem_estoque'
        : '1 AS tem_estoque';

    $sql = sprintf(
        'SELECT %1$s AS descricao,
                MAX(CASE WHEN preco IS NOT NULL THEN 1 ELSE 0 END) AS tem_preco,
                %2$s
         FROM %3$s
         GROUP BY %1$s',
        $campoDescricao,
        $sqlEstoque,
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
        $possuiEstoque = ((int) ($row['tem_estoque'] ?? 0)) === 1;

        $status[$descricao] = [
            'tem_preco' => itemDescricaoTemPreco($descricao, $possuiPreco),
            'tem_estoque' => !descricaoIndicaIndisponivel($descricao) && $possuiEstoque,
        ];
    }

    return $status;
}

function carregarCatalogoFormulario(mysqli $conn): array
{
    static $catalogo = null;

    if ($catalogo !== null) {
        return $catalogo;
    }

    $possuiColunaEstoque = colunaExiste($conn, 'bateria', 'estoque');

    $catalogo = [
        'placa_solar' => buscarStatusCatalogoPorDescricao($conn, 'placa_solar', 'painel', $possuiColunaEstoque),
        'bateria' => buscarStatusCatalogoPorDescricao($conn, 'bateria', 'bateria_desc', $possuiColunaEstoque),
        'estrutura_solar' => buscarStatusCatalogoPorDescricao($conn, 'estrutura_solar', 'estrutura_desc', $possuiColunaEstoque),
    ];

    return $catalogo;
}

/** @deprecated Use carregarCatalogoFormulario() */
function carregarStatusPrecosFormulario(mysqli $conn): array
{
    $catalogo = carregarCatalogoFormulario($conn);
    $precos = [];

    foreach ($catalogo as $grupo => $itens) {
        $precos[$grupo] = [];
        foreach ($itens as $descricao => $status) {
            $precos[$grupo][$descricao] = (bool) ($status['tem_preco'] ?? false);
        }
    }

    return $precos;
}

function obterStatusItemCatalogo(array $catalogo, string $grupo, string $descricao): array
{
    return $catalogo[$grupo][$descricao] ?? [
        'tem_preco' => false,
        'tem_estoque' => false,
    ];
}

function itemDisponivelNoFormulario(array $status): bool
{
    return !empty($status['tem_estoque']);
}

function renderOptionCatalogo(string $valor, string $rotulo, array $status): string
{
    if (!itemDisponivelNoFormulario($status)) {
        return '';
    }

    $valorEscapado = htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
    $rotuloEscapado = htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8');
    $flagPreco = !empty($status['tem_preco']) ? '1' : '0';

    return '<option value="' . $valorEscapado . '" data-tem-preco="' . $flagPreco . '">' . $rotuloEscapado . '</option>';
}

/** @deprecated Use renderOptionCatalogo() */
function renderOptionComPreco(string $valor, string $rotulo, bool $temPreco): string
{
    return renderOptionCatalogo($valor, $rotulo, [
        'tem_preco' => $temPreco,
        'tem_estoque' => true,
    ]);
}
