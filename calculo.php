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

$corrente_gerado_placas = $corrente_placa * $numero_placa_serie;

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

if (strtolower($modelo_controlador) === 'mppt') {
    $quantidade_placas = $quantidade_placa_mppt;
} else {
    $quantidade_placas = $quantidade_placa_pwm;
}

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

$corrente_saida = $total_potencia / $tensao_banco_bateria;

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

$sql = '
    SELECT *
    FROM controlador_carga
    WHERE tipo_controle = ?
      AND condicao = 0
    ORDER BY corrente_nominal ASC
';
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('s', $modelo_controlador);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tensao_compativel = (
            (float) $row['tensao_1_vdc'] === (float) $tensao_banco_bateria ||
            (float) $row['tensao_2_vdc'] === (float) $tensao_banco_bateria ||
            (float) $row['tensao_3_vdc'] === (float) $tensao_banco_bateria
        );

        if (
            $row['corrente_nominal'] >= $corrente_controlador_carga &&
            $row['tensao_circuito_aberto'] >= $tensao_entrada_painel &&
            $tensao_compativel &&
            strtolower($row['tipo_controle']) === strtolower($modelo_controlador)
        ) {
            $resultados[] = [
                'descricao' => $row['controlador'],
                'quantidade' => (int) $row['quantidade'],
                'sku' => $row['sku'],
            ];
            break;
        }
    }

    $stmt->close();
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

$tipo_disjuntor = in_array($tensao_sistema, ['127_sistema', '220_sistema'], true) ? 'AC' : 'DC';

$sql = 'SELECT * FROM disjuntor WHERE tipo = ? ORDER BY corrente_nominal ASC';
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('s', $tipo_disjuntor);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['corrente_nominal'] >= $corrente_saida) {
            $resultados[] = [
                'descricao' => $row['descricao'],
                'quantidade' => 1,
                'sku' => $row['sku'],
            ];
            break;
        }
    }

    $stmt->close();
}

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

// DEBUG — remover depois
function debugValor(mixed $value): string
{
    if (is_float($value)) {
        return number_format($value, 2, '.', '');
    }
    if (is_int($value)) {
        return (string) $value;
    }

    return var_export($value, true);
}

echo '<pre style="background:#1a1a2e;color:#eee;padding:1.25rem;margin:1rem;border-radius:8px;font-family:Consolas,monospace;font-size:13px;line-height:1.7;overflow-x:auto;">';
echo "<strong style=\"color:#7dd3fc;\">=== DEBUG — Variáveis do cálculo ===</strong>\n\n";

echo "<span style=\"color:#fbbf24;\">--- Entrada (POST) ---</span>\n";
echo 'regiao: ' . debugValor($regiao) . "\n";
echo 'modelo_controlador: ' . debugValor($modelo_controlador) . "\n";
echo 'modelo_placa: ' . debugValor($modelo_placa) . "\n";
echo 'tensao_sistema: ' . debugValor($tensao_sistema) . "\n";
echo 'modelo_bateria: ' . debugValor($modelo_bateria) . "\n";
echo 'descarga_bateria: ' . debugValor($descarga_bateria) . "\n";
echo 'tensao_bateria: ' . debugValor($tensao_bateria) . "\n";
echo 'autonomia_dias: ' . debugValor($autonomia_dias) . "\n";
echo 'estrutura: ' . debugValor($estrutura) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Equipamentos ---</span>\n";
echo 'equipamentos: ' . debugValor($equipamentos) . "\n";
echo 'total_consumo_diario: ' . debugValor($total_consumo_diario) . "\n";
echo 'total_potencia: ' . debugValor($total_potencia) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Irradiação / rendimento ---</span>\n";
echo 'rendimento_sistema: ' . debugValor($rendimento_sistema) . "\n";
echo 'rendimento_bateria: ' . debugValor($rendimento_bateria) . "\n";
echo 'incidencia_irradiacao_solar: ' . debugValor($incidencia_irradiacao_solar) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Placa solar ---</span>\n";
echo 'potencia_placa: ' . debugValor($potencia_placa) . "\n";
echo 'tensao_placa: ' . debugValor($tensao_placa) . "\n";
echo 'corrente_placa: ' . debugValor($corrente_placa) . "\n";
echo 'quantidade_placa: ' . debugValor($quantidade_placa) . "\n";
echo 'quantidade_string: ' . debugValor($quantidade_string) . "\n";
echo 'potencia_sistema: ' . debugValor($potencia_sistema) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Bateria ---</span>\n";
echo 'tensao_banco_bateria: ' . debugValor($tensao_banco_bateria) . "\n";
echo 'profundidade_descarga: ' . debugValor($profundidade_descarga) . "\n";
echo 'corrente_bateria: ' . debugValor($corrente_bateria) . "\n";
echo 'tensao_celula_bateria: ' . debugValor($tensao_celula_bateria) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Consumo e dimensionamento ---</span>\n";
echo 'total_consumo_diario_corrigido: ' . debugValor($total_consumo_diario_corrigido) . "\n";
echo 'quantidade_placa_mppt: ' . debugValor($quantidade_placa_mppt) . "\n";
echo 'potencia_gerada: ' . debugValor($potencia_gerada) . "\n";
echo 'quantidade_placa_pwm: ' . debugValor($quantidade_placa_pwm) . "\n";
echo 'numero_placa_serie: ' . debugValor($numero_placa_serie) . "\n";
echo 'corrente_consumida_diariamente: ' . debugValor($corrente_consumida_diariamente) . "\n";
echo 'corrente_necessario_banco_bateria: ' . debugValor($corrente_necessario_banco_bateria) . "\n";
echo 'corrente_gerada_sistema_diario_mppt: ' . debugValor($corrente_gerada_sistema_diario_mppt) . "\n";
echo 'corrente_gerada_sistema_diario_pwm: ' . debugValor($corrente_gerada_sistema_diario_pwm) . "\n";
echo 'quantidade_baterias: ' . debugValor($quantidade_baterias) . "\n";
echo 'corrente_consumida_equipamentos: ' . debugValor($corrente_consumida_equipamentos) . "\n";
echo 'corrente_gerado_placas: ' . debugValor($corrente_gerado_placas) . "\n";
echo 'quantidade_bateria: ' . debugValor($quantidade_bateria) . "\n";
echo 'corrrente_banco_bateria: ' . debugValor($corrrente_banco_bateria) . "\n";
echo 'corrente_carregamento_bateria: ' . debugValor($corrente_carregamento_bateria) . "\n";
echo 'corrente_controlador_carga: ' . debugValor($corrente_controlador_carga) . "\n";
echo 'tensao_entrada_painel: ' . debugValor($tensao_entrada_painel) . "\n";
echo 'quantidade_placas: ' . debugValor($quantidade_placas) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Saída / inversor ---</span>\n";
echo 'tensao_saida_carga: ' . debugValor($tensao_saida_carga) . "\n";
echo 'corrente_saida: ' . debugValor($corrente_saida) . "\n";
echo 'tensao_entrada_inversor: ' . debugValor($tensao_entrada_inversor) . "\n";
echo 'tensao_saida_inversor: ' . debugValor($tensao_saida_inversor) . "\n";
echo 'inversor_escolhido: ' . debugValor($inversor_escolhido) . "\n";
echo 'quantidade_inversor: ' . debugValor($quantidade_inversor) . "\n\n";

echo "<span style=\"color:#fbbf24;\">--- Resultados finais ---</span>\n";
echo 'resultados: ' . debugValor($resultados) . "\n";
echo 'tipo_disjuntor: ' . debugValor($tipo_disjuntor) . "\n";
if (isset($placas_por_estrutura)) {
    echo 'placas_por_estrutura: ' . debugValor($placas_por_estrutura) . "\n";
}
if (isset($quantidade_estrutura)) {
    echo 'quantidade_estrutura: ' . debugValor($quantidade_estrutura) . "\n";
}

echo "\n<strong style=\"color:#7dd3fc;\">=== FIM DEBUG ===</strong>";
echo '</pre>';

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
        $totalComponentes = array_sum(array_map(fn ($item) => (int) ($item['quantidade'] ?? 1), $componentes));
        $categoriasUnicas = count(array_unique(array_column($componentes, 'categoria')));
        ?>

        <?php if (empty($componentes)): ?>
            <section class="card card--result">
                <p class="result-empty">Nenhum componente encontrado.</p>
            </section>
        <?php else: ?>
            <div class="componentes-resumo">
                <div class="stat-box">
                    <div class="stat-box__icon stat-box__icon--blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        </svg>
                    </div>
                    <div class="stat-box__content">
                        <div class="stat-box__label">Total de itens</div>
                        <div class="stat-box__value"><?= $totalItens ?></div>
                        <div class="stat-box__hint">Itens dimensionados</div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-box__icon stat-box__icon--violet" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="stat-box__content">
                        <div class="stat-box__label">Total de componentes</div>
                        <div class="stat-box__value"><?= $totalComponentes ?></div>
                        <div class="stat-box__hint">Soma das quantidades</div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-box__icon stat-box__icon--orange" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                    </div>
                    <div class="stat-box__content">
                        <div class="stat-box__label">Categorias</div>
                        <div class="stat-box__value"><?= $categoriasUnicas ?></div>
                        <div class="stat-box__hint">Tipos de componentes</div>
                    </div>
                </div>
            </div>

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

            <div class="componentes-ilustracao" aria-hidden="true">
                <svg viewBox="0 0 200 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M30 110h140v20H30z" fill="#DBEAFE" opacity="0.5"/>
                    <path d="M60 50 L100 20 L140 50 L140 110 L60 110 Z" fill="#EFF6FF" stroke="#93C5FD" stroke-width="2"/>
                    <path d="M70 60h25v20H70zM105 60h25v20h-25z" fill="#BFDBFE" stroke="#60A5FA" stroke-width="1.5"/>
                    <rect x="145" y="75" width="30" height="35" rx="3" fill="#DBEAFE" stroke="#60A5FA" stroke-width="1.5"/>
                    <circle cx="160" cy="92" r="6" fill="#2563EB" opacity="0.3"/>
                </svg>
            </div>
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