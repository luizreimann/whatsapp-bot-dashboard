import React from 'react';
import BaseNode from './BaseNode';
import { useFlowStore } from '../store';

export default function IntegrationNode({ data, selected }) {
    const { integrations } = useFlowStore();
    const integration = integrations.find((i) => i.id === data.integrationId);

    const actionLabels = {
        sync_lead: 'Sincronizar lead',
        create_deal: 'Criar negócio',
        add_note: 'Adicionar nota',
    };

    let preview;
    if (integration) {
        preview = (
            <>
                <div>{integration.name}</div>
                {data.action && (
                    <div style={{ marginTop: '4px', fontSize: '11px', color: 'var(--fb-text-muted)' }}>
                        {actionLabels[data.action] || data.action}
                    </div>
                )}
            </>
        );
    } else {
        preview = '(integração não selecionada)';
    }

    return (
        <BaseNode
            icon="🔗"
            title={data.label || 'Integração'}
            preview={preview}
            nodeType="integration"
            selected={selected}
        />
    );
}
