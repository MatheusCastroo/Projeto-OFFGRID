<?php
$equipamentos = [
    "geladeira" => [
        "quantidade" => 3,
        "potencia_w" => 15,
        "tempo_hora_dia" => 12

    ],

    "televisao" => [
        "quantidade" => 1,
        "potencia_w" => 20,
        "tempo_hora_dia" => 2
    ],
    "microondas" => [
        "quantidade" => 1,
        "potencia_w" => 65,
        "tempo_hora_dia" => 2
    ]
];

$pontencia_consumida_diaria_1 = $equipamentos["geladeira"]["quantidade"] * $equipamentos["geladeira"]["potencia_w"] * $equipamentos["geladeira"]["tempo_hora_dia"];
$pontencia_consumida_diaria_2 = $equipamentos["televisao"]["quantidade"] * $equipamentos["televisao"]["potencia_w"] * $equipamentos["televisao"]["tempo_hora_dia"];
$pontencia_consumida_diaria_3 = $equipamentos["microondas"]["quantidade"] * $equipamentos["microondas"]["potencia_w"] * $equipamentos["microondas"]["tempo_hora_dia"];

$pontencia_consumida_w_1 = $equipamentos["geladeira"]["quantidade"] * $equipamentos["geladeira"]["potencia_w"];
$potencia_consumida_w_2 = $equipamentos["televisao"]["quantidade"] * $equipamentos["televisao"]["potencia_w"];
$potencia_consumida_w_3 = $equipamentos["microondas"]["quantidade"] * $equipamentos["microondas"]["potencia_w"];

echo "Geladeira: " . $pontencia_consumida_diaria_1 . "W";
echo "<br>";
echo "Televisão: " . $pontencia_consumida_diaria_2 . "W";
echo "<br>";        
echo "Microondas: " . $pontencia_consumida_diaria_3 . "W";
echo "<br>";

$potencia_total_equipamentos = $pontencia_consumida_diaria_1 + $pontencia_consumida_diaria_2 + $pontencia_consumida_diaria_3;
echo "Potência total consumida pelos equipamentos: " . $potencia_total_equipamentos;

$potencia_total_diaria = ($pontencia_consumida_diaria_1 + $pontencia_consumida_diaria_2 + $pontencia_consumida_diaria_3) / 0.95;
echo "<br>";        
echo "Potência total consumida diariamente: " . number_format($potencia_total_diaria,2) . "W";

$potencia_total_equipamentos_w = $pontencia_consumida_w_1 + $potencia_consumida_w_2 + $potencia_consumida_w_3;
echo "<br>";
echo "Potência total consumida pelos equipamentos: " . number_format($potencia_total_equipamentos_w,2) . "W";

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