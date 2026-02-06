/**
 * Inicializa máscaras e CEP lookup nas páginas de onboarding
 */
import { initMasks } from '../utils/input-masks';
import { initCepLookup } from '../utils/cep-lookup';

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar máscaras em todos os inputs com data-mask
    initMasks();

    // Inicializar busca de CEP (step 2)
    initCepLookup('#zip', {
        street: '#street',
        neighborhood: '#neighborhood',
        city: '#city',
        state: '#state',
    });
});
