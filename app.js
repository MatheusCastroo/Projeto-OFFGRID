(function () {
    'use strict';

    const equipList = document.getElementById('equip-list');
    const btnAdd = document.getElementById('btn-adicionar');
    const form = document.getElementById('form-dimensionamento');
    const totalEquipamentos = document.getElementById('total-equipamentos');
    const consumoTotal = document.getElementById('consumo-total');

    function parseNum(value) {
        const n = parseFloat(String(value).replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    }

    function formatWh(wh) {
        if (wh >= 1000) {
            return { value: (wh / 1000).toFixed(2), unit: 'kWh/dia' };
        }
        return { value: Math.round(wh).toString(), unit: 'Wh/dia' };
    }

    function updateRowTotal(row) {
        const qtd = parseNum(row.querySelector('[name="quantidade[]"]')?.value);
        const pot = parseNum(row.querySelector('[name="potencia[]"]')?.value);
        const hrs = parseNum(row.querySelector('[name="horas[]"]')?.value);
        const wh = qtd * pot * hrs;
        const el = row.querySelector('.equip-row__total-value');
        if (el) {
            el.textContent = wh >= 1000 ? (wh / 1000).toFixed(2) + ' kWh' : Math.round(wh) + ' Wh';
        }
    }

    function updateSummary() {
        const rows = equipList.querySelectorAll('.equip-row');
        let consumo = 0;

        rows.forEach((row) => {
            updateRowTotal(row);
            const qtd = parseNum(row.querySelector('[name="quantidade[]"]')?.value);
            const pot = parseNum(row.querySelector('[name="potencia[]"]')?.value);
            const hrs = parseNum(row.querySelector('[name="horas[]"]')?.value);
            consumo += qtd * pot * hrs;

            const removeBtn = row.querySelector('.btn-remove');
            if (removeBtn) removeBtn.hidden = rows.length <= 1;
        });

        totalEquipamentos.textContent = rows.length;

        const formatted = formatWh(consumo);
        consumoTotal.textContent = formatted.value;
        const unidadeEl = document.getElementById('consumo-unidade');
        if (unidadeEl) unidadeEl.textContent = formatted.unit;
    }

    function bindRowEvents(row) {
        row.querySelectorAll('input').forEach((input) => {
            input.addEventListener('input', updateSummary);
            input.addEventListener('blur', () => validateField(input));
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
        row.querySelector('[name="nome[]"]')?.focus();
    }

    function validateField(field) {
        const group = field.closest('.form-group');
        const errorEl = group?.querySelector('.form-error');
        let message = '';

        if (field.hasAttribute('required') && !String(field.value).trim()) {
            message = 'Campo obrigatório.';
        } else if (field.type === 'number' && field.value !== '') {
            const min = field.min !== '' ? parseFloat(field.min) : null;
            const val = parseNum(field.value);
            if (min !== null && val < min) message = 'Valor mínimo: ' + min + '.';
        }

        field.classList.toggle('is-invalid', !!message);
        if (errorEl) errorEl.textContent = message;
        return !message;
    }

    function validateForm() {
        let valid = true;
        form.querySelectorAll('[required]').forEach((field) => {
            if (!validateField(field)) valid = false;
        });

        const rows = equipList.querySelectorAll('.equip-row');
        let hasEquip = false;
        rows.forEach((row) => {
            if (row.querySelector('[name="nome[]"]')?.value.trim()) hasEquip = true;
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

    btnAdd?.addEventListener('click', createEquipRow);
    equipList.querySelectorAll('.equip-row').forEach(bindRowEvents);
    updateSummary();

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
