<?php

require_once 'database.php';

?>

<!DOCTYPE html>

<html lang="pt-BR">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dimensionamento Solar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">

</head>



<body>



    <div class="page">

        <form method="post" action="calculo.php" id="form-dimensionamento" novalidate>



            <header class="page-header">

                <div class="page-header__brand">

                    <div class="icon-box icon-box--lg" aria-hidden="true">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>

                        </svg>

                    </div>

                    <div>

                        <h1 class="page-header__title">Dimensionamento Solar</h1>

                        <p class="page-header__subtitle">Configure os equipamentos e parâmetros do sistema</p>

                    </div>

                </div>

                <div class="page-header__actions">

                    <button type="submit" class="btn btn-primary" id="btnCalcular">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                            <rect x="4" y="2" width="16" height="20" rx="2"/>

                            <path d="M8 6h8M8 10h8M8 14h4"/>

                        </svg>

                        Calcular Sistema

                    </button>

                </div>

            </header>



            <datalist id="equip-sugestoes">
                <option value="Geladeira">
                <option value="Televisão">
                <option value="Microondas">
                <option value="Notebook">
                <option value="Lâmpada LED">
                <option value="Ventilador">
                <option value="Bomba d'água">
                <option value="Ar-condicionado">
            </datalist>

            <div class="container-grid">



                <!-- Equipamentos -->

                <section class="card card--equipamentos" aria-labelledby="titulo-equipamentos">

                    <div class="card__header">

                        <div class="icon-box" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>

                            </svg>

                        </div>

                        <h2 class="card__title" id="titulo-equipamentos">Equipamentos do Cliente</h2>

                    </div>



                    <div class="card__body">

                        <div class="scroll-area equipamentos-lista equip-list" id="equip-list">

                            <div class="equipamento-item equip-row">

                                <div class="form-group form-group--wide">

                                    <label class="form-label">Equipamento</label>

                                    <input type="text" class="input form-input" name="nome[]" list="equip-sugestoes" placeholder="Ex: Geladeira, TV, Microondas..." required>

                                    <span class="form-error"></span>

                                </div>

                                <div class="form-group">

                                    <label class="form-label">Qtd</label>

                                    <input type="number" class="input form-input" name="quantidade[]" min="1" step="1" value="1">

                                    <span class="form-error"></span>

                                </div>

                                <div class="form-group">

                                    <label class="form-label">Potência (W)</label>

                                    <input type="number" class="input form-input" name="potencia[]" min="0" step="1" value="100">

                                    <span class="form-error"></span>

                                </div>

                                <div class="form-group">

                                    <label class="form-label">Horas/dia</label>

                                    <input type="number" class="input form-input" name="horas[]" min="0" max="24" step="0.5" value="8">

                                    <span class="form-error"></span>

                                </div>

                                <div class="equip-row__total">

                                    <span class="form-label">Total</span>

                                    <span class="equip-row__total-value">100 W</span>

                                </div>

                                <button type="button" class="btn-remove" title="Remover" hidden aria-label="Remover equipamento">

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>

                                </button>

                            </div>

                        </div>



                        <button type="button" class="btn btn-add" id="btn-adicionar">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>

                            Adicionar Equipamento

                        </button>

                    </div>



                    <div class="summary-stats card__footer">

                        <div class="stat-box">

                            <div class="stat-box__icon stat-box__icon--blue" aria-hidden="true">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>

                                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>

                                </svg>

                            </div>

                            <div class="stat-box__content">

                                <div class="stat-box__label">Total de equipamentos</div>

                                <div class="stat-box__value" id="total-equipamentos">1</div>

                            </div>

                        </div>

                        <div class="stat-box">

                            <div class="stat-box__icon stat-box__icon--violet" aria-hidden="true">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>

                            </div>

                            <div class="stat-box__content">

                                <div class="stat-box__label">Potência total</div>

                                <div class="stat-box__value">

                                    <span id="potencia-total">100</span>

                                    <span class="stat-box__unit">W</span>

                                </div>

                            </div>

                        </div>

                        <div class="stat-box">

                            <div class="stat-box__icon stat-box__icon--orange" aria-hidden="true">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>

                            </div>

                            <div class="stat-box__content">

                                <div class="stat-box__label">Consumo total</div>

                                <div class="stat-box__value">

                                    <span id="consumo-total">800</span>

                                    <span class="stat-box__unit" id="consumo-unidade">Wh/dia</span>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>



                <!-- Configuração do sistema -->

                <section class="card card--config" aria-labelledby="titulo-config">

                    <div class="card__header">

                        <div class="icon-box" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                <circle cx="12" cy="12" r="3"/>

                                <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>

                            </svg>

                        </div>

                        <h2 class="card__title" id="titulo-config">Configuração do Sistema Solar</h2>

                    </div>



                    <div class="card__body">

                    <div class="form-grid">

                        <div class="form-group">

                            <label class="form-label" for="regiao">Região <span class="required">*</span></label>

                            <select id="regiao" name="regiao" class="form-select" required>

                                <option value="">Selecione...</option>

                                <option value="sul">Sul</option>

                                <option value="norte">Norte</option>

                                <option value="centro-oeste">Centro-Oeste</option>

                                <option value="sudeste">Sudeste</option>

                                <option value="nordeste">Nordeste</option>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="modelo_controlador">Modelo do Controlador <span class="required">*</span></label>

                            <select id="modelo_controlador" name="modelo_controlador" class="form-select" required>

                                <option value="">Selecione...</option>

                                <?php

                                $sql = "SELECT DISTINCT tipo_controle FROM controlador_carga";

                                $result = $conn->query($sql);

                                while ($row = $result->fetch_assoc()) {

                                    echo "<option value='{$row['tipo_controle']}'>{$row['tipo_controle']}</option>";

                                }

                                ?>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="modelo_placa">Modelo da Placa Solar <span class="required">*</span></label>

                            <select id="modelo_placa" name="modelo_placa" class="form-select" required>

                                <option value="">Selecione...</option>

                                <?php

                                $sql = "SELECT DISTINCT painel FROM placa_solar";

                                $result = $conn->query($sql);

                                while ($row = $result->fetch_assoc()) {

                                    echo "<option value='{$row['painel']}'>{$row['painel']}</option>";

                                }

                                ?>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="tensao_sistema">Tensão do Sistema (V) <span class="required">*</span></label>

                            <select id="tensao_sistema" name="tensao_sistema" class="form-select" required>

                                <option value="">Selecione...</option>

                                <option value="12_sistema">12 VDC</option>

                                <option value="24_sistema">24 VDC</option>

                                <option value="48_sistema">48 VDC</option>

                                <option value="127_sistema">127 VCA</option>

                                <option value="220_sistema">220 VCA</option>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="modelo_bateria">Modelo da Bateria <span class="required">*</span></label>

                            <select id="modelo_bateria" name="modelo_bateria" class="form-select" required>

                                <option value="">Selecione...</option>

                                <?php

                                $sql = "SELECT DISTINCT bateria_desc FROM bateria";

                                $result = $conn->query($sql);

                                while ($row = $result->fetch_assoc()) {

                                    echo "<option value='{$row['bateria_desc']}'>{$row['bateria_desc']}</option>";

                                }

                                ?>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="descarga_bateria">Descarga da Bateria (%) <span class="required">*</span></label>

                            <select id="descarga_bateria" name="descarga_bateria" class="form-select" required>

                                <option value="">Selecione...</option>

                                <?php

                                for ($i = 3; $i <= 9; $i++) {

                                    $valor = $i / 10;

                                    $porcentagem = $i * 10;

                                    $selected = ($porcentagem === 50) ? ' selected' : '';

                                    echo "<option value='{$valor}'{$selected}>{$porcentagem}%</option>";

                                }

                                ?>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="tensao_bateria">Tensão Banco de Baterias (V) <span class="required">*</span></label>

                            <select id="tensao_bateria" name="tensao_bateria" class="form-select" required>

                                <option value="">Selecione...</option>

                                <option value="12_bateria">12 VDC</option>

                                <option value="24_bateria">24 VDC</option>

                                <option value="48_bateria">48 VDC</option>

                            </select>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group">

                            <label class="form-label" for="autonomia">Autonomia (dias) <span class="required">*</span></label>

                            <input type="number" id="autonomia" name="autonomia" class="input form-input" min="1" step="1" value="2" required>

                            <span class="form-error"></span>

                        </div>



                        <div class="form-group form-grid--full">

                            <label class="form-label" for="estrutura">Tipo de Estrutura <span class="required">*</span></label>

                            <select id="estrutura" name="estrutura" class="form-select" required>

                                <option value="">Selecione...</option>

                                <?php

                                $sql = "SELECT DISTINCT estrutura_desc FROM estrutura_solar";

                                $result = $conn->query($sql);

                                while ($row = $result->fetch_assoc()) {

                                    echo "<option value='{$row['estrutura_desc']}'>{$row['estrutura_desc']}</option>";

                                }

                                ?>

                            </select>

                            <span class="form-error"></span>

                        </div>

                    </div>



                    <div class="config-note">

                        <div class="icon-box icon-box--sm" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                <rect x="2" y="7" width="20" height="14" rx="2"/>

                                <path d="M6 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/>

                            </svg>

                        </div>

                        <p>Todos os campos marcados com <span class="required">*</span> são obrigatórios para o dimensionamento.</p>

                    </div>

                    </div>

                </section>

            </div>



            <!-- Resumo do sistema -->

            <footer class="system-summary resumo">

                <div class="system-summary__house" aria-hidden="true">
                    <svg viewBox="0 0 180 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M90 15L20 65h15v45h50V80h30v30h15V65h15L90 15z" fill="white" opacity="0.9"/>
                        <rect x="55" y="72" width="18" height="18" rx="2" fill="white" opacity="0.6"/>
                        <rect x="107" y="72" width="18" height="18" rx="2" fill="white" opacity="0.6"/>
                        <rect x="30" y="28" width="28" height="18" rx="2" fill="white" opacity="0.5" transform="rotate(-35 44 37)"/>
                        <rect x="62" y="22" width="28" height="18" rx="2" fill="white" opacity="0.5"/>
                        <rect x="94" y="22" width="28" height="18" rx="2" fill="white" opacity="0.5"/>
                        <rect x="126" y="28" width="28" height="18" rx="2" fill="white" opacity="0.5" transform="rotate(35 140 37)"/>
                        <rect x="145" y="78" width="22" height="32" rx="3" fill="white" opacity="0.7"/>
                        <rect x="149" y="82" width="6" height="10" rx="1" fill="white" opacity="0.4"/>
                        <circle cx="160" cy="70" r="8" fill="white" opacity="0.5"/>
                    </svg>
                </div>

                <div class="system-summary__inner">

                    <div class="system-summary__intro">

                        <div class="system-summary__icon" aria-hidden="true">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                <circle cx="12" cy="12" r="4"/>

                                <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>

                            </svg>

                        </div>

                        <div>

                            <h2 class="system-summary__title">Resumo do Sistema</h2>

                            <p class="system-summary__subtitle">Veja os principais resultados após a configuração</p>

                        </div>

                    </div>



                    <div class="system-summary__metrics">

                        <div class="summary-item">
                            <span class="summary-item__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                            </span>
                            <span class="summary-item__label">Potência FV</span>
                            <span class="summary-item__value">-- <small>Wp</small></span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-item__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/></svg>
                            </span>
                            <span class="summary-item__label">Banco de Baterias</span>
                            <span class="summary-item__value">-- <small>V</small></span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-item__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            </span>
                            <span class="summary-item__label">Capacidade</span>
                            <span class="summary-item__value">-- <small>Ah</small></span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-item__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            </span>
                            <span class="summary-item__label">Energia Armazenada</span>
                            <span class="summary-item__value">-- <small>Wh</small></span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-item__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            </span>
                            <span class="summary-item__label">Autonomia</span>
                            <span class="summary-item__value">-- <small>dias</small></span>
                        </div>

                    </div>

                </div>

            </footer>



            <p class="page-footer-note">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                    <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>

                </svg>

                Cálculos baseados nas melhores práticas do setor.

            </p>



        </form>

    </div>



    <template id="equip-template">

        <div class="equipamento-item equip-row">

            <div class="form-group form-group--wide">

                <label class="form-label">Equipamento</label>

                <input type="text" class="input form-input" name="nome[]" list="equip-sugestoes" placeholder="Ex: Geladeira, TV, Microondas...">

                <span class="form-error"></span>

            </div>

            <div class="form-group">

                <label class="form-label">Qtd</label>

                <input type="number" class="input form-input" name="quantidade[]" min="1" step="1" value="1">

                <span class="form-error"></span>

            </div>

            <div class="form-group">

                <label class="form-label">Potência (W)</label>

                <input type="number" class="input form-input" name="potencia[]" min="0" step="1" value="100">

                <span class="form-error"></span>

            </div>

            <div class="form-group">

                <label class="form-label">Horas/dia</label>

                <input type="number" class="input form-input" name="horas[]" min="0" max="24" step="0.5" value="8">

                <span class="form-error"></span>

            </div>

            <div class="equip-row__total">

                <span class="form-label">Total</span>

                <span class="equip-row__total-value">100 W</span>

            </div>

            <button type="button" class="btn-remove" title="Remover" aria-label="Remover equipamento">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>

            </button>

        </div>

    </template>



    <script src="app.js"></script>

</body>



</html>

