import React from 'react';
import BaseNode from './BaseNode';

export default function EndNode({ data, selected }) {
    const preview = data.message 
        ? (data.message.length > 50 ? data.message.substring(0, 50) + '...' : data.message)
        : '(sem mensagem de encerramento)';

    return (
        <BaseNode
            icon="⏹️"
            title={data.label || 'Fim'}
            preview={preview}
            nodeType="end"
            showSourceHandle={false}
            selected={selected}
        />
    );
}
