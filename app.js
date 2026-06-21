(function () {
    'use strict';

    const equipList = document.getElementById('equip-list');
    const btnAdd = document.getElementById('btn-adicionar');
    const btnClear = document.getElementById('btn-limpar');
    const form = document.getElementById('form-dimensionamento');
    const totalEquipamentos = document.getElementById('total-equipamentos');
    const consumoTotal = document.getElementById('consumo-total');

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
    }

    function bindRowEvents(row) {
        row.querySelectorAll('input, select').forEach((field) => {
            field.addEventListener('input', updateSummary);
            field.addEventListener('change', updateSummary);
            field.addEventListener('blur', () => validateField(field));
        });

        row.querySelector('.btn-remove')?.addEventListener('click', () => {
            if (equipList.querySelectorAll('.equip-row').length > 1) {
                row.remove();
                updateSummary();
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
                first.classList.add('is-invalid');
                const err = first.closest('.form-group')?.querySelector('.form-error');
                if (err) err.textContent = 'Informe ao menos um equipamento.';
            }
        }

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

    form?.addEventListener('submit', (e) => {
        if (!validateForm()) {
            e.preventDefault();
            form.querySelector('.is-invalid')?.focus();
        }
    });

    form?.querySelectorAll('select[required], input[required]').forEach((field) => {
        field.addEventListener('change', () => validateField(field));
        field.addEventListener('blur', () => validateField(field));
    });
})();
