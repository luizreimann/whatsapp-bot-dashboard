document.addEventListener('DOMContentLoaded', () => {
    /**
     * Handler genérico de clique para qualquer botão/elemento de cópia.
     * Funciona com:
     *  - elemento com [data-copy]
     *  - elemento com .copy-trigger
     */
    document.addEventListener('click', async (event) => {
        const trigger = event.target.closest('[data-copy], .copy-trigger');
        if (!trigger) return;

        event.preventDefault();

        // 1) Descobrir o container associado
        //    - prioridade: atributo data-copy-target com seletor CSS
        //    - fallback: ancestral com .copyable-field
        let container = null;

        const targetSelector = trigger.getAttribute('data-copy-target');
        if (targetSelector) {
            container = document.querySelector(targetSelector);
        }

        if (!container) {
            container = trigger.closest('.copyable-field') || trigger;
        }

        if (!container) return;

        // 2) Descobrir o valor a ser copiado
        //    prioridade:
        //     - data-copy-value no container
        //     - data-copy-value no botão
        //     - texto do container (innerText)
        let value =
            container.getAttribute('data-copy-value') ||
            trigger.getAttribute('data-copy-value') ||
            container.innerText;

        value = (value || '').trim();
        if (!value) return;

        // 3) Tentar copiar
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(value);
            } else {
                const temp = document.createElement('textarea');
                temp.value = value;
                temp.style.position = 'fixed';
                temp.style.opacity = '0';
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
            }

            // 4) Feedback visual
            const icon = trigger.querySelector('i');
            const originalIconClass = icon ? icon.className : null;

            // adiciona classe genérica de sucesso pra você estilizar no CSS
            trigger.classList.add('is-copy-success');

            if (icon) {
                icon.className = 'fa-solid fa-check';
            }

            setTimeout(() => {
                trigger.classList.remove('is-copy-success');
                if (icon && originalIconClass) {
                    icon.className = originalIconClass;
                }
            }, 1200);
        } catch (error) {
            console.error('Erro ao copiar valor', error);
        }
    });
});