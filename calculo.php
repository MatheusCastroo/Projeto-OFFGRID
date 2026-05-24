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



echo "Retorno de cada campo: <br>";
echo "Região: $regiao <br>";
echo "Modelo da Placa: $modelo_placa <br>";
echo "Tensão do Sistema: $tensao_sistema <br>";
echo "Modelo da Bateria: $modelo_bateria <br>";
echo "Descarga da Bateria: $descarga_bateria <br>";
echo "Tensão da Bateria: $tensao_bateria <br>";
echo "Autonomia (dias): $autonomia_dias <br>";
echo "Estrutura de Montagem: $estrutura <br>";


$equipamentos = [];

for ($i = 0; $i < count($nomes); $i++){ //Vai validar a quantidade de equpamentos, onde o for so vai parar quando chegar ate o ultimo equip.
    $equipamentos[] = [
    "nome" => $nomes[$i],
    "quantidade" => $quantidades[$i],
    "potencia_w" => $potencias[$i],
    "tempo_hora_dia" => $horas[$i]
    ];
}   

$total_consumo_diario = 0;
$total_potencia = 0;

foreach ($equipamentos as $equipamento) {

    $nome = $equipamento["nome"];
    $quantidade = $equipamento["quantidade"] ?? 0;
    $potencia = $equipamento["potencia_w"] ?? 0;
    $horas = $equipamento["tempo_hora_dia"] ?? 0;

    $consumo = $quantidade * $potencia * $horas;
    $potencia_total_item = $quantidade * $potencia;

    $total_consumo_diario += $consumo;
    $total_potencia += $potencia_total_item;

    echo "Equipamento: $nome | Consumo: $consumo Wh/dia <br>";
}
echo "<br>Total consumo: $total_consumo_diario Wh/dia<br>";
echo "Potência total: $total_potencia W<br>";

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

$quantidade_placa_pwm = ceil((($total_consumo_diario_corrigido / (($tensao_banco_bateria * 1.2) * $incidencia_irradiacao_solar))) / $corrente_placa);
echo "Calculo quantidade placa PWM : $quantidade_placa_pwm <br>";

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

$corrente_controlador_carga = ceil(max($corrente_consumida_equipamentos, $corrente_gerado_placas, $corrente_carregamento_bateria));
echo "Corrente do controlador de carga: $corrente_controlador_carga A <br>";

$tensao_entrada_painel = $tensao_placa * $numero_placa_serie;
echo "Tensão de entrada do painel: " . number_format($tensao_entrada_painel, 2) . " V <br>";        


// ===========✅ AQUI VAI BUSCAR OS RESULTADOS✅=============
echo "=== Resultados ===<br>";
if ($modelo_controlador == "mppt") {
    
    echo "Quantidade de placas:<li>" . $quantidade_placa_mppt . "</li>";
}   
else {
    echo "Quantidade de placas:<li>" . $quantidade_placa_pwm . "</li>";
}
$sql = "SELECT * FROM placa_solar WHERE painel = '$modelo_placa'";
$result = $conn->query($sql);

if($result && $row = $result->fetch_assoc()) {
   $sku = $row['sku'];
    echo "SKU da placa: " . $sku;
}
else {
    echo "Nenhum resultado encontrado para o modelo de placa selecionado.";
}

echo "<br>";
echo "Quantidade de baterias: <li>" . $quantidade_bateria . "</li>"; 
$sql = "SELECT * FROM bateria WHERE bateria_desc = '$modelo_bateria'";
$result = $conn->query($sql);

if($result && $row = $result->fetch_assoc()) {
    $sku_bateria = $row['sku'];
    echo "SKU da bateria: " . $sku_bateria;
}
else {
    echo "Nenhum resultado encontrado para o modelo de bateria selecionado.";
}
echo "<br>";
$inversor_sku = "25414";
$inversor_desc = "INVERSOR SENOIDAL 2000W 24V/110V IP2000-21 EPEVER";
$potencia_trabalho = 1600;

// validação principal
if ($total_potencia <= $potencia_trabalho) { //Verificar a regra com PED, pois a celula esta bloqueada.

    $uso_inversor = ($total_potencia / $potencia_trabalho) * 100;

    echo "SKU do inversor: " . $inversor_sku . "<br>";  
    echo "Descrição do inversor: " . $inversor_desc . "<br>";
    echo "Potência informada: " . $total_potencia . "W<br>";
    echo "Uso do inversor: " . round($uso_inversor, 2) . "%<br>";

    // inteligência de status
    if ($uso_inversor > 90) {
        echo "⚠️ Inversor próximo do limite.<br>";
    } elseif ($uso_inversor > 70) {
        echo "✅ Uso ideal.<br>";
    } else {
        echo "🟢 Sistema com folga.<br>";
    }

} else {
    echo "❌ Inversor NÃO suporta a carga informada.";
}

$sql = "SELECT * FROM controlador_carga";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $sku_controlador = $row['sku']; 
    $controlador = $row['controlador'];
    $corrente_nominal = $row['corrente_nominal'];   
    $tensao_circuito_aberto = $row['tensao_circuito_aberto'];
    $tensao_1_vdc = $row['tensao_1_vdc'];
    $tensao_2_vdc = $row['tensao_2_vdc'];       
    $tensao_3_vdc = $row['tensao_3_vdc'];
    $tipo_controle = $row['tipo_controle'];
    $controlador_quantidade = $row['quantidade'];

    if ((
        $corrente_nominal >= $corrente_controlador_carga
        && $tensao_circuito_aberto >= $tensao_entrada_painel
        && $modelo_controlador == $tipo_controle
        && (
            $tensao_1_vdc == $tensao_banco_bateria
            || $tensao_2_vdc
            || $tensao_banco_bateria
            || $tensao_3_vdc == $tensao_banco_bateria
        )
    )) {
        echo "SKU do controlador de carga: " . $sku_controlador . "<br>";
        echo "Modelo do controlador de carga: " . $controlador . "<br>";
        echo "Tipo de controle: " . $tipo_controle . "<br>";
        echo "Quantidade do controlador: " . $controlador_quantidade;
    } else {
        echo "❌ Controlador de carga selecionado NÃO é compatível com o sistema.";
    }       
}




?>