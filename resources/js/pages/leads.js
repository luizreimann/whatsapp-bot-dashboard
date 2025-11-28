document.addEventListener('DOMContentLoaded', () => {
    const leadsWrapper = document.getElementById('leadsTableWrapper');
    if (!leadsWrapper) return;

    const dataUrl = leadsWrapper.getAttribute('data-url');

    leadsWrapper.addEventListener('click', async (event) => {
        const button = event.target.closest('.sort-link');
        if (!button) return;

        event.preventDefault();

        const sort = button.getAttribute('data-sort');
        const direction = button.getAttribute('data-direction') || 'asc';

        const url = new URL(dataUrl, window.location.origin);
        url.searchParams.set('sort', sort);
        url.searchParams.set('direction', direction);

        url.searchParams.set('page', 1);

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                console.error('Erro ao carregar leads ordenados');
                return;
            }

            const data = await response.json();

            leadsWrapper.innerHTML = data.html;

            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('sort', sort);
            newUrl.searchParams.set('direction', direction);
            newUrl.searchParams.set('page', 1);
            window.history.replaceState({}, '', newUrl.toString());
        } catch (e) {
            console.error('Erro na requisição de ordenação', e);
        }
    });


    // Funcionalidade de copiar para área de transferência
    const copyContainers = document.querySelectorAll('.copyable-field');
    copyContainers.forEach(container => {
        const button = container.querySelector('.copy-trigger');
        if (!button) return;

        button.addEventListener('click', async () => {
            const value = container.getAttribute('data-copy-value') || '';
            if (!value) return;

            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(value);
                } else {
                    // fallback simples
                    const temp = document.createElement('textarea');
                    temp.value = value;
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                }

                // feedback visual rápido
                const icon = button.querySelector('i');
                const oldClass = icon.className;

                icon.className = 'fa-solid fa-check';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-secondary');

                setTimeout(() => {
                    icon.className = oldClass;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 1200);
            } catch (e) {
                console.error('Erro ao copiar valor', e);
            }
        });
    });
});