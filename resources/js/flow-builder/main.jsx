import React from 'react';
import { createRoot } from 'react-dom/client';
import FlowBuilder from './FlowBuilder';
import './styles.css';

const container = document.getElementById('flow-builder-root');

if (container) {
    const flux = JSON.parse(container.dataset.flux || 'null');
    const tenantId = container.dataset.tenantId;
    const integrations = JSON.parse(container.dataset.integrations || '[]');
    const saveUrl = container.dataset.saveUrl;
    const backUrl = container.dataset.backUrl;
    const fluxName = container.dataset.fluxName || flux?.name || 'Novo Fluxo';

    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <FlowBuilder 
                flux={flux}
                tenantId={tenantId}
                integrations={integrations}
                saveUrl={saveUrl}
                backUrl={backUrl}
                initialFluxName={fluxName}
            />
        </React.StrictMode>
    );
}
