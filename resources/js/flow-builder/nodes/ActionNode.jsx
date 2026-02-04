import React from 'react';
import BaseNode from './BaseNode';

export default function ActionNode({ data, selected }) {
    const actionLabels = {
        save_lead: 'Salvar lead',
        update_lead: 'Atualizar lead',
        add_tag: 'Adicionar tag',
        remove_tag: 'Remover tag',
    };

    const preview = actionLabels[data.actionType] || 'Ação';
    const tagInfo = data.actionType === 'add_tag' && data.config?.tag 
        ? `: "${data.config.tag}"` 
        : '';

    return (
        <BaseNode
            icon="⚡"
            title={data.label || 'Ação'}
            preview={preview + tagInfo}
            nodeType="action"
            selected={selected}
        />
    );
}
