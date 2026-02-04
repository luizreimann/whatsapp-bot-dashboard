import React from 'react';
import BaseNode from './BaseNode';

export default function VariableNode({ data, selected }) {
    const operationLabels = {
        set: 'Definir',
        append: 'Concatenar',
        increment: 'Incrementar',
        decrement: 'Decrementar',
    };

    return (
        <BaseNode
            selected={selected}
            icon="💾"
            title={data.label || 'Variável'}
            nodeType="variable"
        >
            <div className="custom-node-preview">
                {data.variableName ? (
                    <>
                        <div style={{ fontFamily: 'monospace', fontSize: '11px' }}>
                            {`{{${data.variableName}}}`}
                        </div>
                        <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                            {operationLabels[data.operation] || 'Definir'}: {data.value || '(vazio)'}
                        </div>
                    </>
                ) : (
                    '(variável não configurada)'
                )}
            </div>
        </BaseNode>
    );
}
