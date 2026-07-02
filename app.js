(function () {
    'use strict';

    const equipList = document.getElementById('equip-list');
    const btnAdd = document.getElementById('btn-adicionar');
    const btnClear = document.getElementById('btn-limpar');
    const form = document.getElementById('form-dimensionamento');
    const btnCalcular = document.getElementById('btnCalcular');
    const totalEquipamentos = document.getElementById('total-equipamentos');
    const consumoTotal = document.getElementById('consumo-total');

    const CAMPOS_OBRIGATORIOS = [
        'regiao',
        'modelo_controlador',
        'modelo_placa',
        'tensao_sistema',
        'modelo_bateria',
        'descarga_bateria',
        'tensao_bateria',
        'autonomia',
        'estrutura',
    ];

    const CAMPOS_CATALOGO = [
        'modelo_placa',
        'modelo_bateria',
        'estrutura',
    ];

    function parseNum(value) {
        const n = parseFloat(String(value).replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    }

    function formatNumeroBR(value, decimals = 0) {
        return new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        }).format(value);
    }

    function formatWh(wh) {
        if (wh >= 1000) {
            return { value: formatNumeroBR(wh / 1000, 2), unit: 'kWh/dia' };
        }
        return { value: formatNumeroBR(Math.round(wh), 0), unit: 'Wh/dia' };
    }

    function potenciaEquipamentoW(qtd, pot) {
        return qtd * pot;
    }

    function updateScrollState() {
        if (!equipList) return;

        if (equipList.childElementCount > 3) {
            equipList.classList.add('scroll-active');
        } else {
            equipList.classList.remove('scroll-active');
        }
    }

    function updateRowTotal(row) {
        const qtd = parseNum(row.querySelector('[name="quantidade[]"]')?.value);
        const pot = parseNum(row.querySelector('[name="potencia[]"]')?.value);
        const potenciaW = potenciaEquipamentoW(qtd, pot);
        const el = row.querySelector('.equip-row__total-value');
        if (el) {
            el.textContent = potenciaW >= 1000
                ? formatNumeroBR(potenciaW / 1000, 2) + ' kW'
                : formatNumeroBR(Math.round(potenciaW), 0) + ' W';
        }
    }

    function updateSummary() {
        const rows = equipList.querySelectorAll('.equip-row');
        let consumo = 0;
        let potenciaTotal = 0;

        rows.forEach((row) => {
            updateRowTotal(row);
            const qtd = parseNum(row.querySelector('[name="quantidade[]"]')?.value);
            const pot = parseNum(row.querySelector('[name="potencia[]"]')?.value);
            const hrs = parseNum(row.querySelector('[name="horas[]"]')?.value);
            const potenciaW = potenciaEquipamentoW(qtd, pot);

            potenciaTotal += potenciaW;
            consumo += potenciaW * hrs;

            const removeBtn = row.querySelector('.btn-remove');
            if (removeBtn) removeBtn.hidden = rows.length <= 1;
        });

        totalEquipamentos.textContent = formatNumeroBR(rows.length, 0);

        const potenciaTotalEl = document.getElementById('potencia-total');
        if (potenciaTotalEl) {
            potenciaTotalEl.textContent = potenciaTotal >= 1000
                ? formatNumeroBR(potenciaTotal / 1000, 2)
                : formatNumeroBR(Math.round(potenciaTotal), 0);
            const potenciaUnidade = potenciaTotalEl.nextElementSibling;
            if (potenciaUnidade?.classList.contains('stat-box__unit')) {
                potenciaUnidade.textContent = potenciaTotal >= 1000 ? 'kW' : 'W';
            }
        }

        const formatted = formatWh(consumo);
        consumoTotal.textContent = formatted.value;
        const unidadeEl = document.getElementById('consumo-unidade');
        if (unidadeEl) unidadeEl.textContent = formatted.unit;

        updateScrollState();
        updateSubmitButton();
    }

    function setFieldError(field, message) {
        if (!field) return;

        const group = field.closest('.form-group');
        const errorEl = group?.querySelector('.form-error');

        field.classList.toggle('is-invalid', !!message);
        if (errorEl) errorEl.textContent = message;
    }

    function optionSemPreco(select) {
        if (!select || !select.value) return false;

        const option = select.selectedOptions[0];
        return option?.dataset.temPreco === '0';
    }

    function validarSelectCatalogo(select, showMessage = true) {
        if (!select) return true;

        let message = '';

        if (select.value && optionSemPreco(select)) {
            const descricao = select.selectedOptions[0]?.textContent.trim() || 'Item selecionado';
            message = descricao + ' não possui preço cadastrado.';
        }

        if (showMessage) {
            setFieldError(select, message);
        } else if (!message) {
            setFieldError(select, '');
        }

        return !message;
    }

    function validarTodosCatalogos(showMessages = true) {
        return CAMPOS_CATALOGO.every((name) => {
            const field = form?.querySelector(`[name="${name}"]`);
            return validarSelectCatalogo(field, showMessages);
        });
    }

    function sanitizarSelectsCatalogo() {
        CAMPOS_CATALOGO.forEach((name) => {
            const select = form?.querySelector(`[name="${name}"]`);
            if (!select || !select.value) return;

            const opcaoValida = Array.from(select.options).some(
                (option) => option.value === select.value && option.value !== ''
            );

            if (!opcaoValida) {
                select.selectedIndex = 0;
                setFieldError(select, '');
            }
        });
    }

    function equipamentoLinhaPreenchida(row) {
        const nome = row.querySelector('[name="nome[]"]')?.value.trim();
        const qtd = parseNum(row.querySelector('[name="quantidade[]"]')?.value);
        const pot = parseNum(row.querySelector('[name="potencia[]"]')?.value);
        const hrs = parseNum(row.querySelector('[name="horas[]"]')?.value);

        return !!nome && qtd > 0 && pot > 0 && hrs > 0;
    }

    function formularioCompleto() {
        if (!form || !equipList) return false;

        const camposOk = CAMPOS_OBRIGATORIOS.every((name) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (!field) return false;

            const valor = String(field.value ?? '').trim();
            if (valor === '') return false;

            if (field.type === 'number') {
                const numero = parseNum(field.value);
                if (!Number.isFinite(numero) || numero <= 0) return false;
            }

            return true;
        });

        if (!camposOk) return false;

        const rows = equipList.querySelectorAll('.equip-row');
        const linhasPreenchidas = Array.from(rows).filter(equipamentoLinhaPreenchida);

        return linhasPreenchidas.length > 0;
    }

    function updateSubmitButton() {
        if (!btnCalcular) return;

        const completo = formularioCompleto();
        const catalogoOk = validarTodosCatalogos(false);
        const habilitar = completo && catalogoOk;

        btnCalcular.disabled = !habilitar;
    }

    function bindRowEvents(row) {
        row.querySelectorAll('input, select').forEach((field) => {
            field.addEventListener('input', () => {
                updateSummary();
                updateSubmitButton();
            });
            field.addEventListener('change', () => {
                updateSummary();
                updateSubmitButton();
            });
            field.addEventListener('blur', () => {
                validateField(field);
                updateSubmitButton();
            });
        });

        row.querySelector('.btn-remove')?.addEventListener('click', () => {
            if (equipList.querySelectorAll('.equip-row').length > 1) {
                row.remove();
                updateSummary();
                updateSubmitButton();
            }
        });
    }

    function createEquipRow() {
        const template = document.getElementById('equip-template');
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.equip-row');
        bindRowEvents(row);
        equipList.appendChild(clone);
        updateSummary();

        if (equipList.classList.contains('scroll-active')) {
            equipList.scrollTop = equipList.scrollHeight;
        }

        row.querySelector('[name="nome[]"]')?.focus();
    }

    function validateField(field) {
        const group = field.closest('.form-group');
        const errorEl = group?.querySelector('.form-error');
        let message = '';

        if (field.tagName === 'SELECT' && CAMPOS_CATALOGO.includes(field.name)) {
            return validarSelectCatalogo(field, true);
        }

        if (field.hasAttribute('required') && !String(field.value).trim()) {
            message = 'Campo obrigatório.';
        } else if (field.type === 'number') {
            const val = parseNum(field.value);
            const min = field.min !== '' ? parseFloat(field.min) : null;

            if (field.value === '' || !Number.isFinite(val)) {
                if (field.name === 'potencia[]' || field.name === 'horas[]') {
                    message = 'Informe um valor maior que 0.';
                }
            } else if (field.name === 'potencia[]' && val <= 0) {
                message = 'Informe um valor maior que 0.';
            } else if (field.name === 'horas[]' && val <= 0) {
                message = 'Informe um valor maior que 0.';
            } else if (min !== null && val < min) {
                message = 'Valor mínimo: ' + min + '.';
            }
        }

        field.classList.toggle('is-invalid', !!message);
        if (errorEl) errorEl.textContent = message;
        return !message;
    }

    function validateEquipRow(row) {
        const nome = row.querySelector('[name="nome[]"]')?.value.trim();
        if (!nome) return true;

        const potField = row.querySelector('[name="potencia[]"]');
        const hrsField = row.querySelector('[name="horas[]"]');
        const potOk = validateField(potField);
        const hrsOk = validateField(hrsField);
        return potOk && hrsOk;
    }

    function validateForm() {
        let valid = true;

        form.querySelectorAll('[required]').forEach((field) => {
            if (!validateField(field)) valid = false;
        });

        CAMPOS_CATALOGO.forEach((name) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field && !validarSelectCatalogo(field, true)) valid = false;
        });

        const rows = equipList.querySelectorAll('.equip-row');
        let hasEquip = false;
        rows.forEach((row) => {
            const nome = row.querySelector('[name="nome[]"]')?.value.trim();
            if (nome) {
                hasEquip = true;
                if (!validateEquipRow(row)) valid = false;
            }
        });

        if (!hasEquip) {
            valid = false;
            const first = rows[0]?.querySelector('[name="nome[]"]');
            if (first) {
                setFieldError(first, 'Informe ao menos um equipamento.');
            }
        }

        if (!formularioCompleto()) valid = false;

        updateSubmitButton();
        return valid;
    }

    function restoreFormData(data) {
        if (!data || !form || !equipList) return;

        const nomes = Array.isArray(data.nome) ? data.nome : [data.nome || ''];
        const quantidades = Array.isArray(data.quantidade) ? data.quantidade : [data.quantidade || '1'];
        const potencias = Array.isArray(data.potencia) ? data.potencia : [data.potencia || ''];
        const horasArr = Array.isArray(data.horas) ? data.horas : [data.horas || ''];
        const template = document.getElementById('equip-template');

        if (!template) return;

        equipList.innerHTML = '';

        const count = Math.max(nomes.length, quantidades.length, potencias.length, horasArr.length, 1);
        for (let i = 0; i < count; i++) {
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.equip-row');

            row.querySelector('[name="nome[]"]').value = nomes[i] || '';
            row.querySelector('[name="quantidade[]"]').value = quantidades[i] ?? 1;
            row.querySelector('[name="potencia[]"]').value = potencias[i] ?? '';
            row.querySelector('[name="horas[]"]').value = horasArr[i] ?? '';

            bindRowEvents(row);
            equipList.appendChild(clone);
        }

        [
            'regiao',
            'modelo_controlador',
            'modelo_placa',
            'tensao_sistema',
            'modelo_bateria',
            'descarga_bateria',
            'tensao_bateria',
            'autonomia',
            'estrutura',
        ].forEach((name) => {
            if (data[name] === undefined) return;
            const field = form.querySelector(`[name="${name}"]`);
            if (field) field.value = data[name];
        });

        updateSummary();
        sanitizarSelectsCatalogo();
        CAMPOS_CATALOGO.forEach((name) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) validarSelectCatalogo(field, true);
        });
        updateSubmitButton();
    }

    function bindConfigFields() {
        CAMPOS_OBRIGATORIOS.forEach((name) => {
            const field = form?.querySelector(`[name="${name}"]`);
            if (!field) return;

            field.addEventListener('input', updateSubmitButton);
            field.addEventListener('change', () => {
                if (CAMPOS_CATALOGO.includes(name)) {
                    validarSelectCatalogo(field, true);
                } else {
                    validateField(field);
                }
                updateSubmitButton();
            });
            field.addEventListener('blur', () => {
                if (CAMPOS_CATALOGO.includes(name)) {
                    validarSelectCatalogo(field, true);
                } else {
                    validateField(field);
                }
                updateSubmitButton();
            });
        });
    }

    function clearForm() {
        if (!form || !equipList) return;

        const template = document.getElementById('equip-template');
        if (!template) return;

        equipList.innerHTML = '';
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.equip-row');
        bindRowEvents(row);
        equipList.appendChild(clone);

        form.querySelectorAll('select').forEach((select) => {
            select.selectedIndex = 0;
        });

        const descargaBateria = form.querySelector('#descarga_bateria');
        if (descargaBateria) descargaBateria.value = '0.5';

        const autonomia = form.querySelector('#autonomia');
        if (autonomia) autonomia.value = '2';

        form.querySelectorAll('.is-invalid').forEach((field) => field.classList.remove('is-invalid'));
        form.querySelectorAll('.form-error').forEach((errorEl) => {
            errorEl.textContent = '';
        });

        window.__formRestore = null;
        updateSummary();
        updateSubmitButton();

        if (window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('editar');
            window.history.replaceState({}, '', url);
        }

        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'limpar_dimensionamento=1',
        }).catch(() => {});
    }

    btnAdd?.addEventListener('click', createEquipRow);
    btnClear?.addEventListener('click', () => {
        if (!window.confirm('Deseja limpar todos os campos do formulário?')) return;
        clearForm();
    });

    if (window.__formRestore && typeof window.__formRestore === 'object') {
        restoreFormData(window.__formRestore);
    } else {
        equipList.querySelectorAll('.equip-row').forEach(bindRowEvents);
        updateSummary();
    }

    bindConfigFields();
    sanitizarSelectsCatalogo();
    updateSubmitButton();

    form?.addEventListener('submit', (e) => {
        if (!validateForm()) {
            e.preventDefault();
            form.querySelector('.is-invalid')?.focus();
        }
    });
})();
