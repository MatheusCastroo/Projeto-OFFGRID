<?php

require_once 'database.php';
$equipamentos = [
    "Geladeira" => [
        "quantidade" => 1,
        "potencia_w" => 150,
        "tempo_hora_dia" => 24
        
    ],
    "TV" => [
        "quantidade" => 1,
        "potencia_w" => 100,
        "tempo_hora_dia" => 5
    ],
    "Lâmpadas" => [
        "quantidade" => 5,
        "potencia_w" => 10,
        "tempo_hora_dia" => 6
    ]
]; 

$total_consumo_diario = 0;
$total_potencia = 0;

foreach ($equipamentos as $nome => $equipamento) {

    $quantidade = $equipamento["quantidade"] ?? 0;
    $potencia = $equipamento["potencia_w"] ?? 0;
    $horas = $equipamento["tempo_hora_dia"] ?? 0;

    $consumo = $quantidade * $potencia * $horas;
    $potencia_total_item = $quantidade * $potencia;

    $total_consumo_diario += $consumo;
    $total_potencia += $potencia_total_item;

    echo "Equipamento: $nome | Consumo: $consumo Wh/dia <br>";
}

echo "<br>";
echo "Consumo total DIARIO: $total_consumo_diario Wh/dia <br>";

$total_consumo_diario_corrigido = $total_consumo_diario / 0.95;

echo "Consumo corrigido: " . number_format($total_consumo_diario_corrigido, 2) . " Wh/dia <br>";
echo "Potência total: $total_potencia W <br>";

$regioes = [
    "sul" => 4.2,
    "norte" => 4.55,
    "centro-oeste" => 5.25,
    "sudeste" => 4.55,
    "nordeste" => 5.6  
];

$regiaoEscolhida = "sul";
$incidencia_irradiacao_solar = $regioes[$regiaoEscolhida] ?? 0;

echo "Incidência solar ($regiaoEscolhida): $incidencia_irradiacao_solar kWh/m²/dia";

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
echo "<br><br> Quantidade de placas necessárias (MPPT): $quantidade_placa_mppt <br>";

echo "<br>";
$potencia_gerada = $potencia_placa * $incidencia_irradiacao_solar * $quantidade_placa_mppt;
echo "Potência gerada: " . number_format($potencia_gerada, 2) . " Wh/dia <br>";

$calculo_quantide_placa_pwm = ceil((($total_consumo_diario_corrigido / (($tensao_banco_bateria * 1.2) * $incidencia_irradiacao_solar))) / $corrente_placa);
echo "Calculo quantidade placa PWM : $calculo_quantide_placa_pwm <br>";

$numero_placa_serie = ceil(($tensao_banco_bateria * 1.2) / $tensao_placa);
echo "Número de placas em série: $numero_placa_serie <br>";

$corrente_consumida_diariamente = $total_consumo_diario_corrigido / $tensao_banco_bateria;
echo "Corrente consumida diariamente: " . number_format($corrente_consumida_diariamente , 2) . " A <br>"; 

$corrente_necessario_banco_bateria = ($corrente_consumida_diariamente / $profundidade_descarga) * $autonomia_dias;
echo "Corrente necessário no banco de baterias: " . number_format($corrente_necessario_banco_bateria, 2) . " A <br>";

$corrente_gerada_sistema_diario_mppt = ($potencia_sistema * $incidencia_irradiacao_solar);
echo "Corrente gerada pelo sistema diariamente (MPPT): " . number_format($corrente_gerada_sistema_diario_mppt, 2) . " A <br>";

$corrente_gerada_sistema_diario_pwm = (($tensao_banco_bateria * 1.2) * ($corrente_placa * $quantidade_string)) * $incidencia_irradiacao_solar;
echo "Corrente gerada pelo sistema diariamente (PWM): " . number_format($corrente_gerada_sistema_diario_pwm, 2) . " A <br>";

$quantidade_baterias = ceil($corrente_necessario_banco_bateria / $corrente_bateria);
echo "Quantidade de baterias necessárias: $quantidade_baterias <br>";


// ✅ CORRIGIDO AQUI
$corrente_consumida_equipamentos = $total_potencia / $tensao_banco_bateria;
echo "Corrente consumida pelos equipamentos: " . number_format($corrente_consumida_equipamentos, 2) . " A <br>";

$corrente_gerado_placas = $corrente_placa * $numero_placa_serie;
echo "Corrente gerada pelas placas: " . number_format($corrente_gerado_placas, 2) . " A <br>";  

// ✅ INICIALIZAÇÃO ADICIONADA
$quantidade_bateria = 0;

if ($tensao_banco_bateria >= $tensao_bateria) { 
    $quantidade_bateria = ceil($corrente_necessario_banco_bateria / $corrente_bateria) * ($tensao_banco_bateria / $tensao_bateria);
    echo "Quantidade de baterias necessárias (considerando tensão): $quantidade_bateria <br>";
}
else {
    echo "bateria incompativel";
}
   
echo "<br>";   
$corrrente_banco_bateria = ($quantidade_bateria / ($tensao_banco_bateria / $tensao_bateria)) * $corrente_bateria;
echo "Corrente total do banco de baterias: " . number_format($corrrente_banco_bateria, 2) . " A <br>";

$corrente_carregamento_bateria = $corrrente_banco_bateria * 0.1;
echo "Corrente de carregamento da bateria: " . number_format($corrente_carregamento_bateria, 2) . " A <br>";

$maior_valor_arredondado = ceil(max($corrente_consumida_equipamentos, $corrente_gerado_placas, $corrente_carregamento_bateria));
echo "Maior valor arredondado: $maior_valor_arredondado A <br>";

?>