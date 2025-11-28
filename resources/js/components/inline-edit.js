document.addEventListener('DOMContentLoaded', () => {
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // delegação de eventos global
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-inline-edit]');
        if (!trigger) return;

        event.preventDefault();

        const targetSelector = trigger.getAttribute('data-inline-target');
        const url            = trigger.getAttribute('data-inline-url');
        const field          = trigger.getAttribute('data-inline-field');
        const type           = trigger.getAttribute('data-inline-type') || 'text';

        if (!targetSelector || !url || !field) {
            console.warn('InlineEdit: faltando data-inline-target, data-inline-url ou data-inline-field.');
            return;
        }

        const displayEl = document.querySelector(targetSelector);
        if (!displayEl) {
            console.warn('InlineEdit: elemento alvo não encontrado:', targetSelector);
            return;
        }

        // Evita abrir editor múltiplas vezes
        if (displayEl.dataset.inlineEditing === '1') {
            return;
        }
        displayEl.dataset.inlineEditing = '1';

        const originalText = (displayEl.innerText || '').trim();

        // Criar campo editável
        let inputEl;
        if (type === 'textarea') {
            inputEl = document.createElement('textarea');
            inputEl.rows = 3;
        } else {
            inputEl = document.createElement('input');
            inputEl.type = 'text';
        }

        inputEl.className = 'form-control';
        inputEl.value = originalText === 'Nenhuma anotação adicionada ainda.' ? '' : originalText;

        // Troca o conteúdo visual
        displayEl.style.display = 'none';
        const parent = displayEl.parentElement;
        parent.appendChild(inputEl);
        inputEl.focus();
        inputEl.select();

        let cancelled = false;

        const cleanup = () => {
            displayEl.dataset.inlineEditing = '0';
            inputEl.remove();
            displayEl.style.display = '';
        };

        const saveValue = async () => {
            const newValue = inputEl.value.trim();

            // Se não mudou nada, só fecha
            if (newValue === originalText) {
                cleanup();
                return;
            }

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        [field]: newValue,
                    }),
                });

                if (!response.ok) {
                    console.error('Erro ao salvar inline edit', response.status);
                    cleanup();
                    return;
                }

                const data = await response.json();

                const updatedText =
                    (data[field] ?? newValue) || 'Nenhuma anotação adicionada ainda.';

                displayEl.innerText = updatedText;
                cleanup();
            } catch (e) {
                console.error('Erro na requisição de inline edit', e);
                cleanup();
            }
        };

        inputEl.addEventListener('blur', () => {
            if (cancelled) return;
            // blur dispara depois do keydown às vezes, então damos um small timeout
            setTimeout(() => {
                if (!cancelled) {
                    saveValue();
                }
            }, 50);
        });

        inputEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && type !== 'textarea') {
                e.preventDefault();
                inputEl.blur();
            }

            if (e.key === 'Escape') {
                cancelled = true;
                cleanup();
            }
        });
    });
});