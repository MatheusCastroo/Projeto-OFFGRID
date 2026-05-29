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

for ($i = 0; $i < count($nomes); $i++) {
    $equipamentos[] = [
        'nome' => $nomes[$i],
        'quantidade' => $quantidades[$i],
        'potencia_w' => $potencias[$i],
        'tempo_hora_dia' => $horas[$i],
    ];
}

$total_consumo_diario = 0;
$total_potencia = 0;

foreach ($equipamentos as $equipamento) {
    $nome = $equipamento['nome'];
    $quantidade = $equipamento['quantidade'] ?? 0;
    $potencia = $equipamento['potencia_w'] ?? 0;
    $horas = $equipamento['tempo_hora_dia'] ?? 0;

    $consumo = $quantidade * $potencia * $horas;
    $potencia_total_item = $quantidade * $potencia;

    $total_consumo_diario += $consumo;
    $total_potencia += $potencia_total_item;
}

$total_consumo_diario_corrigido = $total_consumo_diario / 0.95;

$regioes = [
    'sul' => 4.2,
    'norte' => 4.55,
    'centro-oeste' => 5.25,
    'sudeste' => 4.55,
    'nordeste' => 5.6,
];

$regiaoEscolhida = 'sul';
$incidencia_irradiacao_solar = $regioes[$regiaoEscolhida] ?? 0;

$potencia_placa = 560;
$tensao_placa = 50.6;
$corrente_placa = 14.06;
$quantidade_placa = 1;
$quantidade_string = 1;

$potencia_sistema = $potencia_placa * $quantidade_placa;

$tensao_banco_bateria = 24;
$profundidade_descarga = 0.8;
$autonomia_dias = 2;

$corrente_bateria = 7;
$tensao_bateria = 12;

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

if ($tensao_banco_bateria >= $tensao_bateria) {
    $quantidade_bateria = ceil($corrente_necessario_banco_bateria / $corrente_bateria) * ($tensao_banco_bateria / $tensao_bateria);
}

$corrrente_banco_bateria = ($quantidade_bateria / ($tensao_banco_bateria / $tensao_bateria)) * $corrente_bateria;

$corrente_carregamento_bateria = $corrrente_banco_bateria * 0.1;

$corrente_controlador_carga = ceil(max($corrente_consumida_equipamentos, $corrente_gerado_placas, $corrente_carregamento_bateria));

$tensao_entrada_painel = $tensao_placa * $numero_placa_serie;

if ($modelo_controlador == 'mppt') {
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
    $tensao_saida_carga_num = (float) $tensao_saida_carga;
    $stmt->bind_param('dd', $tensao_banco_bateria, $tensao_saida_carga_num);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {

        // REGRA DA PLANILHA:
        // pega o menor inversor que atende a potência
       $potencia_minima = $total_potencia * 1.3;

if ($row['potencia_trabalho'] >= $potencia_minima) {

    $inversor_escolhido = $row;

    // calcula quantidade corretamente
    $quantidade_inversor = ceil(
        $total_potencia / $row['potencia_trabalho']
    );

    // cálculo de uso
    $uso = ($total_potencia / $row['potencia_trabalho']) * 100;

    // você pode usar isso depois no front se quiser
    $inversor_escolhido['uso_percentual'] = round($uso, 2);

    break;
}
    }

    $stmt->close();
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
        'descricao' => $inversor_escolhido['inversor'],
        'quantidade' => $quantidade_inversor,
        'sku' => $inversor_escolhido['sku'],
    ];
}

$sql = 'SELECT * FROM controlador_carga';
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $compativel = (
            $row['corrente_nominal'] >= $corrente_controlador_carga
            && $row['tensao_circuito_aberto'] >= $tensao_entrada_painel
            && $modelo_controlador == $row['tipo_controle']
            && (
                $row['tensao_1_vdc'] == $tensao_banco_bateria
                || $row['tensao_2_vdc']
                || $tensao_banco_bateria
                || $row['tensao_3_vdc'] == $tensao_banco_bateria
            )
        );

        if ($compativel) {
            $resultados[] = [
                'descricao' => $row['controlador'],
                'quantidade' => $row['quantidade'],
                'sku' => $row['sku'],
            ];
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Dimensionamento</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page">
        <header class="page-header">
            <h1 class="page-header__title">Componentes do Sistema</h1>
        </header>

        <?php if (empty($resultados)): ?>
            <p>Nenhum componente encontrado.</p>
        <?php else: ?>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Componente</th>
                        <th>Quantidade</th>
                        <th>SKU</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['descricao']) ?></td>
                            <td><?= (int) $item['quantidade'] ?></td>
                            <td><?= (int) $item['sku'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="index.php">Voltar</a></p>
    </div>
</body>
</html>
