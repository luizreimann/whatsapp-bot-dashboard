/**
 * Módulo reutilizável para busca de CEP via ViaCEP
 * API: https://viacep.com.br/ws/{cep}/json/
 */

/**
 * Inicializa busca automática de CEP
 * @param {string} cepSelector - Seletor do input de CEP
 * @param {Object} fieldMap - Mapa de seletores dos campos de endereço
 */
export function initCepLookup(cepSelector, fieldMap) {
    const cepInput = document.querySelector(cepSelector);
    if (!cepInput) return;

    const spinner = document.querySelector('#cep-spinner');
    const errorDiv = document.querySelector('#cep-error');

    async function fetchCep(cep) {
        const clean = cep.replace(/\D/g, '');
        if (clean.length !== 8) return;

        // Mostrar loading
        if (spinner) spinner.classList.remove('d-none');
        if (errorDiv) errorDiv.classList.add('d-none');

        try {
            const response = await fetch(`https://viacep.com.br/ws/${clean}/json/`);
            const data = await response.json();

            if (data.erro) {
                if (errorDiv) {
                    errorDiv.textContent = 'CEP não encontrado.';
                    errorDiv.classList.remove('d-none');
                }
                return;
            }

            // Preencher campos
            const fields = {
                street: data.logradouro || '',
                neighborhood: data.bairro || '',
                city: data.localidade || '',
                state: data.uf || '',
            };

            Object.entries(fields).forEach(([key, value]) => {
                if (fieldMap[key]) {
                    const el = document.querySelector(fieldMap[key]);
                    if (el) {
                        el.value = value;
                        // Marcar cidade e estado como readonly quando preenchidos
                        if ((key === 'city' || key === 'state') && value) {
                            el.setAttribute('readonly', true);
                        }
                    }
                }
            });

        } catch (err) {
            if (errorDiv) {
                errorDiv.textContent = 'Erro ao buscar CEP. Tente novamente.';
                errorDiv.classList.remove('d-none');
            }
        } finally {
            if (spinner) spinner.classList.add('d-none');
        }
    }

    // Buscar ao sair do campo
    cepInput.addEventListener('blur', () => {
        fetchCep(cepInput.value);
    });

    // Buscar quando atingir 9 caracteres (com máscara: 00000-000)
    cepInput.addEventListener('input', () => {
        const clean = cepInput.value.replace(/\D/g, '');
        if (clean.length === 8) {
            fetchCep(cepInput.value);
        }
    });
}
