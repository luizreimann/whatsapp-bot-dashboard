import React from 'react';
import BaseNode from './BaseNode';

export default function MessageNode({ data, selected }) {
    const preview = data.text 
        ? (data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text)
        : '(mensagem vazia)';

    return (
        <BaseNode
            icon="💬"
            title={data.label || 'Mensagem'}
            preview={preview}
            nodeType="message"
            selected={selected}
        />
    );
}
