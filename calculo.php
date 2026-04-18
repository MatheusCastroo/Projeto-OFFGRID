<?php
$total_consumo = 0;
$total_potencia = 0;

foreach ($equipamentos as $nome => $equipamento) {

    $quantidade = $equipamento["quantidade"];
    $potencia = $equipamento["potencia_w"];
    $horas = $equipamento["tempo_hora_dia"];

    // Consumo diário (Wh)
    $consumo = $quantidade * $potencia * $horas;

    // Potência (W)
    $potencia_total_item = $quantidade * $potencia;

    // Soma nos totais
    $total_consumo += $consumo;
    $total_potencia += $potencia_total_item;

    // Exibir por item
    echo $nome . ": " . $consumo . " Wh/dia";
    echo "<br>";
}

echo "<br>";
echo "Consumo total: " . $total_consumo . " Wh/dia";
echo "<br>";

// Aplicando perda
$consumo_corrigido = $total_consumo / 0.95;

echo "Consumo corrigido: " . number_format($consumo_corrigido, 2) . " Wh/dia";
echo "<br>";

echo "Potência total: " . $total_potencia . " W";

$regioes = [
 "sul" => 4.2,
 "norte" => 4.55,
 "centro-oeste" => 5.25,
 "sudeste" => 4.55,
 "nordeste" => 5.6  
];
echo "<br>";
$incidencia_irradiacao_solar = $regioes["sul"];
echo "Incidência de Irradiação Solar na Região Sul: " . $incidencia_irradiacao_solar . "kWh/m²/dia"; //Puxar de acorodo com a região escolhida
?>