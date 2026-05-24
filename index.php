<?php
require_once 'database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h1>Calculadora de Consumo de Energia</h1>
        <form method="post" action="calculo.php">
            <label for="nome">Equipamentos:</label><br>
            <input type="text" id="nome" name="nome[]"><br><br>
            <label for="quantidade">Quantidade:</label><br>
            <input type="number" id="quantidade" name="quantidade[]"><br><br>
            <label for="potencia">Potência (W):</label><br>
            <input type="number" id="potencia" name="potencia[]"><br><br>
            <label for="horas">Horas de uso por dia:</label><br>
            <input type="number" id="horas" name="horas[]"><br><br>
            <!--input type="submit_adicionar" value="Adicionar Equipamento" id=btnAdicionar><br><br> Utilizar esse botao apos os testes de apenas 1 item. -->

            <div class="dados_sistema">
                    <h2>Dados do Sistema</h2>

                    <label for="regiao">Região:</label><br>
                    <select id="regiao" name="regiao">
                        <option value="sul">Sul</option>
                        <option value="norte">Norte</option>
                        <option value="centro-oeste">Centro-Oeste</option>
                        <option value="sudeste">Sudeste</option>
                        <option value="nordeste">Nordeste</option>
                    </select><br><br>
                    <label for="modelo_controlador">Modelo do Controlador:</label><br>
                    <select id="modelo_controlador" name="modelo_controlador">
                        <option value="">Selecione uma tipo de controle</option>
                        <?php
                        $sql = "SELECT DISTINCT tipo_controle FROM controlador_carga";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['tipo_controle']}'>{$row['tipo_controle']}</option>";
                        }
                        ?>
                    </select><br><br>
                    <label for="modelo_placa">Modelo da Placa:</label><br>
                    <select id="modelo_placa" name="modelo_placa">
                        <option value="">Selecione uma placa</option>
                        <?php
                        $sql = "SELECT DISTINCT painel FROM placa_solar";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['painel']}'>{$row['painel']}</option>";
                        }
                        ?>
                    </select><br><br>
                    <label for="tensao_sistema">Tensão do Sistema:</label><br>
                    <select id="tensao_sistema" name="tensao_sistema">
                        <option value="">Selecione a tensão do sistema</option>
                        <option value="12_sistema">12vdc</option>
                        <option value="24_sistema">24vdc</option>
                        <option value="48_sistema">48vdc</option>
                        <option value="127_sistema">127vca</option>
                        <option value="220_sistema">220vca</option>
                    </select><br><br>
                    <label for="modelo_bateria">Modelo da Bateria:</label><br>
                    <select id="modelo_bateria" name="modelo_bateria">
                        <option value="">Selecione um modelo de bateria</option>
                        <?php
                        $sql = "SELECT DISTINCT bateria_desc FROM bateria";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['bateria_desc']}'>{$row['bateria_desc']}</option>";
                        }
                        ?>
                    </select><br><br>
                    <label for="descarga_bateria">Descarregar bateria até:</label><br>
                    <select id="descarga_bateria" name="descarga_bateria">
                        <option value="">Selecione a profundidade de descarga</option>
                        <?php
                        for ($i = 3; $i <= 9; $i++) {
                            $valor = $i / 10;
                            $porcentagem = $i * 10;
                            echo "<option value='{$valor}'>{$porcentagem}%</option>";
                        }
                        ?>
                    </select><br><br>
                    <label for="tensao_bateria">Tensão do banco da Bateria:</label><br>
                    <select id="tensao_bateria" name="tensao_bateria">
                        <option value="">Selecione a tensão do banco de bateria</option>
                        <option value="12_bateria">12vdc</option>
                        <option value="24_bateria">24vdc</option>
                        <option value="48_bateria">48vdc</option>
                    </select><br><br>

                    <label for="autonomia">Autonomia desejada (em dias):</label><br>
                    <input type="number" id="autonomia" name="autonomia"><br><br>

                    <label for="estrutura">Estrutura de Montagem:</label><br>
                    <select id="estrutura" name="estrutura">
                        <option value="">Selecione a estrutura de montagem</option>
                        <?php
                        $sql = "SELECT DISTINCT estrutura_desc FROM estrutura_solar";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['estrutura_desc']}'>{$row['estrutura_desc']}</option>";
                        }
                        ?>
                    </select><br><br>
            </div>

            <input type="submit" value="Calcular" id=btnCalcular>
        </form>
    </div>
</body>

</html>