import React from 'react';
import BaseNode from './BaseNode';

export default function StartNode({ data, selected }) {
    const triggerText = data.trigger === 'keyword' 
        ? `Palavra-chave: ${data.keyword || '(não definida)'}` 
        : 'Qualquer mensagem';

    return (
        <BaseNode
            icon="▶️"
            title={data.label || 'Início'}
            preview={triggerText}
            nodeType="start"
            showTargetHandle={false}
            selected={selected}
        />
    );
}
