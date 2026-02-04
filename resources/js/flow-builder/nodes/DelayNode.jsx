import React from 'react';
import BaseNode from './BaseNode';

export default function DelayNode({ data, selected }) {
    const unitLabels = {
        seconds: 'segundo(s)',
        minutes: 'minuto(s)',
        hours: 'hora(s)',
    };

    const preview = `${data.duration || 60} ${unitLabels[data.unit] || 'segundo(s)'}`;

    return (
        <BaseNode
            icon="⏱️"
            title={data.label || 'Aguardar'}
            preview={preview}
            nodeType="delay"
            selected={selected}
        />
    );
}
