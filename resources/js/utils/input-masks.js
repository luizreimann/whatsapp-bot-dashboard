/**
 * Módulo reutilizável de máscaras de input
 * Uso: adicionar data-mask="cpf|cnpj|phone|cep" nos inputs
 */

function applyMask(value, pattern) {
    let i = 0;
    const clean = value.replace(/\D/g, '');
    return pattern.replace(/#/g, () => clean[i++] || '');
}

export function maskCpf(input) {
    input.addEventListener('input', () => {
        const raw = input.value.replace(/\D/g, '').slice(0, 11);
        if (raw.length <= 3) {
            input.value = raw;
        } else if (raw.length <= 6) {
            input.value = applyMask(raw, '###.###');
        } else if (raw.length <= 9) {
            input.value = applyMask(raw, '###.###.###');
        } else {
            input.value = applyMask(raw, '###.###.###-##');
        }
    });
}

export function maskCnpj(input) {
    input.addEventListener('input', () => {
        const raw = input.value.replace(/\D/g, '').slice(0, 14);
        if (raw.length <= 2) {
            input.value = raw;
        } else if (raw.length <= 5) {
            input.value = applyMask(raw, '##.###');
        } else if (raw.length <= 8) {
            input.value = applyMask(raw, '##.###.###');
        } else if (raw.length <= 12) {
            input.value = applyMask(raw, '##.###.###/####');
        } else {
            input.value = applyMask(raw, '##.###.###/####-##');
        }
    });
}

export function maskPhone(input) {
    input.addEventListener('input', () => {
        const raw = input.value.replace(/\D/g, '').slice(0, 11);
        if (raw.length <= 2) {
            input.value = raw.length ? `(${raw}` : '';
        } else if (raw.length <= 6) {
            input.value = applyMask(raw, '(##) ####');
        } else if (raw.length <= 10) {
            input.value = applyMask(raw, '(##) ####-####');
        } else {
            input.value = applyMask(raw, '(##) #####-####');
        }
    });
}

export function maskCep(input) {
    input.addEventListener('input', () => {
        const raw = input.value.replace(/\D/g, '').slice(0, 8);
        if (raw.length <= 5) {
            input.value = raw;
        } else {
            input.value = applyMask(raw, '#####-###');
        }
    });
}

/**
 * Inicializa todas as máscaras baseado no atributo data-mask
 */
export function initMasks(container = document) {
    container.querySelectorAll('[data-mask="cpf"]').forEach(maskCpf);
    container.querySelectorAll('[data-mask="cnpj"]').forEach(maskCnpj);
    container.querySelectorAll('[data-mask="phone"]').forEach(maskPhone);
    container.querySelectorAll('[data-mask="cep"]').forEach(maskCep);
}
