import React from 'react';
import { Position } from '@xyflow/react';
import BaseNode from './BaseNode';

export default function ConditionNode({ data, selected }) {
    const operatorLabels = {
        equals: '=',
        not_equals: '≠',
        contains: 'contém',
        not_contains: 'não contém',
        greater: '>',
        less: '<',
        starts_with: 'começa com',
        ends_with: 'termina com',
    };

    const preview = data.variable 
        ? `${data.variable} ${operatorLabels[data.operator] || '='} "${data.value || ''}"`
        : '(condição não configurada)';

    return (
        <BaseNode
            icon="🔀"
            title={data.label || 'Condição'}
            preview={preview}
            nodeType="condition"
            selected={selected}
            sourceHandles={[
                { id: 'true', position: Position.Bottom, style: { left: '30%' } },
                { id: 'false', position: Position.Bottom, style: { left: '70%' } },
            ]}
        >
            <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: '8px', fontSize: '11px' }}>
                <span style={{ color: 'var(--fb-success)' }}>✓ Sim</span>
                <span style={{ color: 'var(--fb-danger)' }}>✕ Não</span>
            </div>
        </BaseNode>
    );
}
