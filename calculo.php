<?php

require_once 'database.php';

$nomes = $_POST['nome'];
$quantidades = $_POST['quantidade'];
$potencias = $_POST['potencia'];
$horas = $_POST['horas'];

$regiao = $_POST['regiao'];
$modelo_controlador = $_POST['modelo_controlador'];
$modelo_placa = $_POST['modelo_placa'];
$tensao_sistema = $_POST['tensao_sistema'];
$modelo_bateria = $_POST['modelo_bateria'];
$descarga_bateria = $_POST['descarga_bateria'];
$tensao_bateria = $_POST['tensao_bateria'];
$autonomia_dias = $_POST['autonomia'];
$estrutura = $_POST['estrutura'];

$equipamentos = [];
$total_consumo_diario = 0;
$total_potencia = 0;

for ($i = 0; $i < count($nomes); $i++) {
    $nome = trim((string) ($nomes[$i] ?? ''));
    if ($nome === '') {
        continue;
    }

    $quantidade = (float) ($quantidades[$i] ?? 0);
    $potencia_w = (float) ($potencias[$i] ?? 0);
    $horas_dia = (float) ($horas[$i] ?? 0);
    $potencia_equipamento_w = $potencia_w;
    $potencia_total_linha = $quantidade * $potencia_w;

    $equipamentos[] = [
        'nome' => $nome,
        'quantidade' => $quantidade,
        'potencia_w' => $potencia_w,
        'potencia_equipamento_w' => $potencia_total_linha,
        'tempo_hora_dia' => $horas_dia,
    ];

    $total_potencia += $potencia_total_linha;
    $total_consumo_diario += $potencia_equipamento_w * $horas_dia * $quantidade;
}

$rendimento_sistema = 0.95;

$regioes = [
    'sul' => 4.2,
    'norte' => 4.55,
    'centro-oeste' => 5.25,
    'sudeste' => 4.55,
    'nordeste' => 5.6,
];

$incidencia_irradiacao_solar = $regioes[$regiao] ?? 0;

$potencia_placa = 0;
$tensao_placa = 0;
$corrente_placa = 0;
$quantidade_placa = 1;
$quantidade_string = 1;

$stmt = $conn->prepare('SELECT potencia_max, tensao_circuito, corrente_curto FROM placa_solar WHERE painel = ? LIMIT 1');
$stmt->bind_param('s', $modelo_placa);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $potencia_placa = (float) $row['potencia_max'];
    $tensao_placa = (float) $row['tensao_circuito'];
    $corrente_placa = (float) $row['corrente_curto'];
}
$stmt->close();

$potencia_sistema = $potencia_placa * $quantidade_placa;

$tensao_banco_bateria = (int) filter_var($tensao_bateria, FILTER_SANITIZE_NUMBER_INT);
$profundidade_descarga = (float) $descarga_bateria;
$autonomia_dias = (int) $autonomia_dias;

$corrente_bateria = 0;
$tensao_celula_bateria = 0;
$rendimento_bateria = null;

$stmt = $conn->prepare('SELECT tensao_nominal_vdc, capacidade_bateria, rendimento_bateria FROM bateria WHERE bateria_desc = ? LIMIT 1');
$stmt->bind_param('s', $modelo_bateria);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $tensao_celula_bateria = (float) $row['tensao_nominal_vdc'];
    $corrente_bateria = (float) $row['capacidade_bateria'];
    $rendimento_bateria = (float) $row['rendimento_bateria'] / 100;
}
$stmt->close();

$total_consumo_diario = round($total_consumo_diario / $rendimento_sistema, 2);
$total_consumo_diario_corrigido = $total_consumo_diario;

$quantidade_placa_mppt = ceil($total_consumo_diario_corrigido / ($potencia_placa * $incidencia_irradiacao_solar));

$potencia_gerada = $potencia_placa * $incidencia_irradiacao_solar * $quantidade_placa_mppt;

$quantidade_placa_pwm = ceil((($total_consumo_diario_corrigido / (($tensao_banco_bateria * 1.2) * $incidencia_irradiacao_solar))) / $corrente_placa);

$numero_placa_serie = ceil(($tensao_banco_bateria * 1.2) / $tensao_placa);

$corrente_consumida_diariamente = $total_consumo_diario_corrigido / $tensao_banco_bateria;

$corrente_necessario_banco_bateria = ($corrente_consumida_diariamente / $profundidade_descarga) * $autonomia_dias;

$corrente_gerada_sistema_diario_mppt = ($potencia_sistema * $incidencia_irradiacao_solar);

$corrente_gerada_sistema_diario_pwm = (($tensao_banco_bateria * 1.2) * ($corrente_placa * $quantidade_string)) * $incidencia_irradiacao_solar;

$quantidade_baterias = ceil($corrente_necessario_banco_bateria / $corrente_bateria);

$corrente_consumida_equipamentos = $total_potencia / $tensao_banco_bateria;

$isMppt = strtolower($modelo_controlador) === 'mppt';
$quantidade_placas = $isMppt ? $quantidade_placa_mppt : $quantidade_placa_pwm;

$strings_paralelas = $numero_placa_serie > 0
    ? (int) ceil($quantidade_placas / $numero_placa_serie)
    : $quantidade_placas;
$corrente_gerado_placas = $corrente_placa * $strings_paralelas;

$quantidade_bateria = 0;

if ($tensao_banco_bateria >= $tensao_celula_bateria && $corrente_bateria > 0) {
    $quantidade_bateria = ceil($corrente_necessario_banco_bateria / $corrente_bateria) * ($tensao_banco_bateria / $tensao_celula_bateria);
}

$corrrente_banco_bateria = 0;
if ($tensao_celula_bateria > 0 && $quantidade_bateria > 0) {
    $corrrente_banco_bateria = ($quantidade_bateria / ($tensao_banco_bateria / $tensao_celula_bateria)) * $corrente_bateria;
}

$corrente_carregamento_bateria = $corrrente_banco_bateria * 0.1;

$corrente_controlador_carga = ceil(max($corrente_consumida_equipamentos, $corrente_gerado_placas, $corrente_carregamento_bateria));

$tensao_entrada_painel = $tensao_placa * $numero_placa_serie;

// PROCV(Projeto!$H$6; Bateria!I4:J9; 2; FALSO)
$tensao_saida_carga = null;
$sql = 'SELECT valor FROM tensoes WHERE valor = ? LIMIT 1';
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('s', $tensao_sistema);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $tensao_saida_carga = $row['valor'];
    }
    $stmt->close();
}

$inversor_escolhido = null;
$quantidade_inversor = 0;

$tensao_entrada_inversor = (int) filter_var($_POST['tensao_bateria'] ?? '', FILTER_SANITIZE_NUMBER_INT);
$tensao_saida_inversor = match ($tensao_sistema) {
    '127_sistema' => 127,
    '220_sistema' => 220,
    default => null,
};

if ($tensao_saida_inversor && $tensao_entrada_inversor) {
    $sql = '
        SELECT *
        FROM inversor
        WHERE tensao_entrada = ?
          AND tensao_saida = ?
          AND condicao = 0
        ORDER BY potencia_trabalho ASC
    ';

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('dd', $tensao_entrada_inversor, $tensao_saida_inversor);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $potencia_minima = $total_potencia * 1.3;

            if ($row['potencia_trabalho'] >= $potencia_minima) {
                $inversor_escolhido = $row;
                $quantidade_inversor = (int) ceil($total_potencia / $row['potencia_trabalho']);
                $inversor_escolhido['uso_percentual'] = round(($total_potencia / $row['potencia_trabalho']) * 100, 2);
                break;
            }
        }

        $stmt->close();
    }
}

// =========== Resultados (somente exibição) =============
$resultados = [];

$stmt = $conn->prepare('SELECT sku, painel FROM placa_solar WHERE painel = ? LIMIT 1');
$stmt->bind_param('s', $modelo_placa);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $resultados[] = [
        'descricao' => $row['painel'],
        'quantidade' => $quantidade_placas,
        'sku' => $row['sku'],
    ];
}
$stmt->close();

$stmt = $conn->prepare('SELECT sku, bateria_desc FROM bateria WHERE bateria_desc = ? LIMIT 1');
$stmt->bind_param('s', $modelo_bateria);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $resultados[] = [
        'descricao' => $row['bateria_desc'],
        'quantidade' => $quantidade_bateria,
        'sku' => $row['sku'],
    ];
}
$stmt->close();

if ($inversor_escolhido) {
    $resultados[] = [
        'sku' => $inversor_escolhido['sku'],
        'inversor_desc' => $inversor_escolhido['inversor'],
        'quantidade' => $quantidade_inversor ?: 1,
    ];
}

$controlador_escolhido = null;

$sql = '
    SELECT sku, controlador, corrente_nominal, tensao_circuito_aberto,
           tensao_1_vdc, tensao_2_vdc, tensao_3_vdc, tipo_controle, quantidade
    FROM controlador_carga
    WHERE tipo_controle = ?
      AND condicao = 0
      AND corrente_nominal >= ?
      AND tensao_circuito_aberto >= ?
    ORDER BY quantidade ASC, sku ASC
';

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('sdd', $modelo_controlador, $corrente_controlador_carga, $tensao_entrada_painel);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $compativel_banco = false;

        foreach (['tensao_1_vdc', 'tensao_2_vdc', 'tensao_3_vdc'] as $campo) {
            if ($row[$campo] === null || $row[$campo] === '') {
                continue;
            }

            if ((int) (float) $row[$campo] === $tensao_banco_bateria) {
                $compativel_banco = true;
                break;
            }
        }

        if (!$compativel_banco) {
            continue;
        }

        $controlador_escolhido = $row;
        break;
    }

    $stmt->close();
}

if ($controlador_escolhido) {
    $resultados[] = [
        'descricao' => $controlador_escolhido['controlador'],
        'quantidade' => (int) $controlador_escolhido['quantidade'],
        'sku' => $controlador_escolhido['sku'],
    ];
}

$stmt = $conn->prepare('SELECT sku, estrutura_desc, quantidade FROM estrutura_solar WHERE estrutura_desc = ? LIMIT 1');
$stmt->bind_param('s', $estrutura);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $placas_por_estrutura = max(1, (int) $row['quantidade']);
    $quantidade_estrutura = $row['quantidade'] > 0
        ? (int) ceil($quantidade_placas / $placas_por_estrutura)
        : 0;

    if ($quantidade_estrutura > 0) {
        $resultados[] = [
            'descricao' => $row['estrutura_desc'],
            'quantidade' => $quantidade_estrutura,
            'sku' => $row['sku'],
        ];
    }
}
$stmt->close();

$disjuntor_dc_escolhido = null;
$disjuntor_ac_escolhido = null;

if ($controlador_escolhido && $controlador_escolhido['quantidade'] > 0) {
    $corrente_controlador_unidade = $controlador_escolhido['corrente_nominal']
        / $controlador_escolhido['quantidade'];

    $sql = '
        SELECT sku, descricao, corrente_nominal
        FROM disjuntor
        WHERE tipo = ?
          AND corrente_nominal > ?
        ORDER BY corrente_nominal ASC, sku ASC
    ';

    $tipo_disjuntor_dc = 'DC';
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('sd', $tipo_disjuntor_dc, $corrente_controlador_unidade);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $disjuntor_dc_escolhido = $row;
        }

        $stmt->close();
    }

    if ($disjuntor_dc_escolhido) {
        $resultados[] = [
            'descricao' => $disjuntor_dc_escolhido['descricao'],
            'quantidade' => (int) $controlador_escolhido['quantidade'],
            'sku' => $disjuntor_dc_escolhido['sku'],
        ];
    } else {
        $resultados[] = [
            'descricao' => 'Indisponivel',
            'quantidade' => (int) $controlador_escolhido['quantidade'],
        ];
    }
}

$corrente_disjuntor_ac = $tensao_saida_inversor > 0
    ? $total_potencia / $tensao_saida_inversor
    : 0;

if ($corrente_disjuntor_ac > 0) {
    $sql = '
        SELECT sku, descricao, corrente_nominal
        FROM disjuntor
        WHERE tipo = ?
          AND corrente_nominal >= ?
        ORDER BY corrente_nominal ASC, sku ASC
    ';

    $tipo_disjuntor_ac = 'AC';
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('sd', $tipo_disjuntor_ac, $corrente_disjuntor_ac);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $disjuntor_ac_escolhido = $row;
        }

        $stmt->close();
    }

    if ($disjuntor_ac_escolhido) {
        $resultados[] = [
            'descricao' => $disjuntor_ac_escolhido['descricao'],
            'quantidade' => 1,
            'sku' => $disjuntor_ac_escolhido['sku'],
        ];
    }
}

$energia_necessaria_kw = $total_consumo_diario_corrigido / 1000;

if ($isMppt) {
    $energia_gerada_kw = ($potencia_placa * $incidencia_irradiacao_solar * $quantidade_placa_mppt) / 1000;
} else {
    $energia_gerada_kw = ((($tensao_banco_bateria * 1.2) * ($corrente_placa * $strings_paralelas)) * $incidencia_irradiacao_solar) / 1000;
}

$bateria_necessaria_ah = $corrente_consumida_diariamente;
$bateria_gerada_ah = $corrrente_banco_bateria;

$inversor_necessario_w = $total_potencia;
$inversor_gerado_w = $inversor_escolhido ? (float) $inversor_escolhido['potencia_trabalho'] : 0;

$controlador_necessario = $corrente_controlador_carga;
$controlador_gerado = $controlador_escolhido ? (float) $controlador_escolhido['corrente_nominal'] : 0;

function detectarCategoriaComponente(string $descricao): string
{
    $texto = mb_strtoupper($descricao, 'UTF-8');

    if (str_contains($texto, 'PAINEL') || str_contains($texto, 'PLACA') || str_contains($texto, 'FOTOVOLTAIC')) {
        return 'painel';
    }
    if (str_contains($texto, 'BATERIA')) {
        return 'bateria';
    }
    if (str_contains($texto, 'CONTROLADOR')) {
        return 'controlador';
    }
    if (str_contains($texto, 'DISJUNTOR')) {
        return 'disjuntor';
    }
    if (str_contains($texto, 'INVERSOR')) {
        return 'inversor';
    }
    if (str_contains($texto, 'ESTRUTURA')) {
        return 'estrutura';
    }

    return 'outro';
}

function extrairNomeComponente(string $descricao): string
{
    $partes = preg_split('/\s+/', trim($descricao), -1, PREG_SPLIT_NO_EMPTY);
    if (!$partes) {
        return $descricao;
    }

    $nome = [];
    foreach ($partes as $parte) {
        $nome[] = $parte;
        if (count($nome) >= 3 || preg_match('/\d/', $parte)) {
            break;
        }
    }

    return implode(' ', $nome);
}

$categoriasLabel = [
    'painel' => 'Painel solar',
    'bateria' => 'Bateria',
    'controlador' => 'Controlador de carga',
    'disjuntor' => 'Disjuntor',
    'inversor' => 'Inversor',
    'estrutura' => 'Estrutura',
    'outro' => 'Componente',
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Dimensionamento</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page">
        <header class="page-header">
            <div class="page-header__brand">
                <div class="icon-box icon-box--lg" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="page-header__title">Componentes do Sistema</h1>
                    <p class="page-header__subtitle">Lista de materiais dimensionados</p>
                </div>
            </div>
            <div class="page-header__actions">
                <a href="index.php" class="btn btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Voltar
                </a>
            </div>
        </header>

        <?php
        $componentes = [];
        foreach ($resultados as $item) {
            $descricao = $item['descricao'] ?? $item['inversor_desc'] ?? $item['disjuntor_desc'] ?? null;
            if (!$descricao) {
                continue;
            }
            $categoria = detectarCategoriaComponente($descricao);
            $componentes[] = array_merge($item, [
                'descricao' => $descricao,
                'categoria' => $categoria,
                'nome_curto' => extrairNomeComponente($descricao),
                'categoria_label' => $categoriasLabel[$categoria] ?? $categoriasLabel['outro'],
            ]);
        }

        $unicos = [];
        foreach ($componentes as $item) {
            $sku = $item['sku'] ?? uniqid('item_', true);
            $unicos[$sku] = $item;
        }

        if ($inversor_escolhido && !isset($unicos[$inversor_escolhido['sku']])) {
            $descricaoInversor = $inversor_escolhido['inversor'];
            $categoriaInversor = detectarCategoriaComponente($descricaoInversor);
            $unicos[$inversor_escolhido['sku']] = [
                'descricao' => $descricaoInversor,
                'quantidade' => $quantidade_inversor ?: 1,
                'sku' => $inversor_escolhido['sku'],
                'categoria' => $categoriaInversor,
                'nome_curto' => extrairNomeComponente($descricaoInversor),
                'categoria_label' => $categoriasLabel[$categoriaInversor] ?? $categoriasLabel['outro'],
            ];
        }

        $componentes = array_values($unicos);
        $totalItens = count($componentes);
        ?>

        <?php if (empty($componentes)): ?>
            <section class="card card--result">
                <p class="result-empty">Nenhum componente encontrado.</p>
            </section>
        <?php else: ?>
            <section class="card card--result">
                <div class="componentes-toolbar">
                    <div class="componentes-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input
                            type="search"
                            id="busca-componente"
                            class="componentes-search__input"
                            placeholder="Buscar componente ou SKU..."
                            autocomplete="off"
                        >
                    </div>
                    <select id="filtro-categoria" class="componentes-filter select" aria-label="Filtrar por categoria">
                        <option value="">Todos</option>
                        <?php
                        $categoriasPresentes = array_unique(array_column($componentes, 'categoria'));
                        sort($categoriasPresentes);
                        foreach ($categoriasPresentes as $cat):
                        ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($categoriasLabel[$cat] ?? $cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="componentes-lista">
                    <div class="componentes-lista__header" aria-hidden="true">
                        <span>Componente</span>
                        <span>Quantidade</span>
                        <span>SKU</span>
                    </div>

                    <div class="componentes-lista__body" id="lista-componentes">
                        <?php foreach ($componentes as $item):
                            $quantidade = (int) ($item['quantidade'] ?? 1);
                            $sku = (string) ($item['sku'] ?? '—');
                            $categoria = $item['categoria'] ?? 'outro';
                        ?>
                            <article
                                class="componente-item componente-item--<?= htmlspecialchars($categoria) ?>"
                                data-categoria="<?= htmlspecialchars($categoria) ?>"
                                data-busca="<?= htmlspecialchars(mb_strtolower($item['descricao'] . ' ' . $sku, 'UTF-8')) ?>"
                            >
                                <div class="componente-item__info">
                                    <div class="componente-item__icon" aria-hidden="true">
                                        <?php if ($categoria === 'painel'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                                        <?php elseif ($categoria === 'bateria'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="18" height="10" rx="2"/><path d="M22 11v2"/><path d="M6 11v2M10 11v2M14 11v2"/></svg>
                                        <?php elseif ($categoria === 'controlador'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/></svg>
                                        <?php elseif ($categoria === 'disjuntor'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                        <?php elseif ($categoria === 'inversor'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 12H6M6 12l4-4M6 12l4 4"/></svg>
                                        <?php elseif ($categoria === 'estrutura'): ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20h20M5 20V8l7-5 7 5v12"/></svg>
                                        <?php else: ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="componente-item__texto">
                                        <h3 class="componente-nome"><?= htmlspecialchars($item['nome_curto']) ?></h3>
                                        <p class="componente-descricao"><?= htmlspecialchars($item['descricao']) ?></p>
                                    </div>
                                </div>
                                <div class="componente-item__quantidade">
                                    <span class="quantidade-badge"><?= $quantidade ?></span>
                                    <span class="quantidade-unidade"><?= $quantidade === 1 ? 'unidade' : 'unidades' ?></span>
                                </div>
                                <div class="componente-item__sku">
                                    <span class="sku-tag"><?= htmlspecialchars($sku) ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <p class="componentes-lista__vazio" id="lista-vazia" hidden>Nenhum componente corresponde à busca.</p>

                    <footer class="componentes-lista__footer">
                        <p class="componentes-lista__status" id="lista-status">
                            Mostrando <?= $totalItens ?> de <?= $totalItens ?> itens
                        </p>
                        <div class="componentes-paginacao" aria-label="Paginação">
                            <button type="button" class="componentes-paginacao__btn" disabled aria-label="Página anterior">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <span class="componentes-paginacao__atual">1</span>
                            <button type="button" class="componentes-paginacao__btn" disabled aria-label="Próxima página">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                        </div>
                    </footer>
                </div>
            </section>

            <section class="card card--result resumo-dimensionamento">
                <div class="resumo-dimensionamento__intro">
                    <h2 class="resumo-dimensionamento__title">Resumo de dimensionamento</h2>
                    <p class="resumo-dimensionamento__subtitle">Valores calculados para o sistema fotovoltaico</p>
                </div>

                <div class="componentes-lista componentes-lista--resumo">
                    <div class="componentes-lista__header" aria-hidden="true">
                        <span>Componente</span>
                        <span>Necessário</span>
                        <span>Gerada</span>
                        <span>Unidade</span>
                    </div>

                    <div class="componentes-lista__body">
                        <article class="componente-item resumo-item resumo-item--energia">
                            <div class="componente-item__info">
                                <div class="componente-item__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                </div>
                                <div class="componente-item__texto">
                                    <h3 class="componente-nome">Energia</h3>
                                </div>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--necessario"><?= number_format($energia_necessaria_kw, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--gerada"><?= number_format($energia_gerada_kw, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__unidade">
                                <span class="resumo-unidade">Kw/P</span>
                            </div>
                        </article>

                        <article class="componente-item resumo-item resumo-item--bateria">
                            <div class="componente-item__info">
                                <div class="componente-item__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="18" height="10" rx="2"/><path d="M22 11v2"/><path d="M6 11v2M10 11v2M14 11v2"/></svg>
                                </div>
                                <div class="componente-item__texto">
                                    <h3 class="componente-nome">Bateria</h3>
                                </div>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--necessario"><?= number_format($bateria_necessaria_ah, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--gerada"><?= number_format($bateria_gerada_ah, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__unidade">
                                <span class="resumo-unidade">Ah</span>
                            </div>
                        </article>

                        <article class="componente-item resumo-item resumo-item--inversor">
                            <div class="componente-item__info">
                                <div class="componente-item__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 12c2-4 6-6 8-6s6 2 8 6M4 12c2 4 6 6 8 6s6-2 8-6"/></svg>
                                </div>
                                <div class="componente-item__texto">
                                    <h3 class="componente-nome">Inversor</h3>
                                </div>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--necessario"><?= number_format($inversor_necessario_w, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--gerada"><?= number_format($inversor_gerado_w, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__unidade">
                                <span class="resumo-unidade">W</span>
                            </div>
                        </article>

                        <article class="componente-item resumo-item resumo-item--controlador">
                            <div class="componente-item__info">
                                <div class="componente-item__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/></svg>
                                </div>
                                <div class="componente-item__texto">
                                    <h3 class="componente-nome">Controlador de carga</h3>
                                </div>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--necessario"><?= number_format($controlador_necessario, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__valor">
                                <span class="resumo-badge resumo-badge--gerada"><?= number_format($controlador_gerado, 2, ',', '.') ?></span>
                            </div>
                            <div class="resumo-item__unidade">
                                <span class="resumo-unidade">Ah</span>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script>
    (function () {
        'use strict';

        const busca = document.getElementById('busca-componente');
        const filtro = document.getElementById('filtro-categoria');
        const itens = document.querySelectorAll('.componente-item');
        const status = document.getElementById('lista-status');
        const vazio = document.getElementById('lista-vazia');
        const total = itens.length;

        if (!busca || !itens.length) return;

        function filtrarComponentes() {
            const termo = busca.value.trim().toLowerCase();
            const categoria = filtro ? filtro.value : '';
            let visiveis = 0;

            itens.forEach((item) => {
                const texto = item.dataset.busca || '';
                const cat = item.dataset.categoria || '';
                const matchBusca = !termo || texto.includes(termo);
                const matchCategoria = !categoria || cat === categoria;
                const visivel = matchBusca && matchCategoria;

                item.hidden = !visivel;
                if (visivel) visiveis++;
            });

            if (status) {
                status.textContent = 'Mostrando ' + visiveis + ' de ' + total + ' itens';
            }
            if (vazio) {
                vazio.hidden = visiveis > 0;
            }
        }

        busca.addEventListener('input', filtrarComponentes);
        filtro?.addEventListener('change', filtrarComponentes);
    })();
    </script>
</body>
</html>