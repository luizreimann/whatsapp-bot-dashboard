import React from 'react';
import BaseNode from './BaseNode';

export default function ReactionNode({ data, selected }) {
    return (
        <BaseNode
            selected={selected}
            icon="💬"
            title={data.label || 'Reação'}
            nodeType="reaction"
        >
            <div className="custom-node-preview">
                {data.emoji ? (
                    <div style={{ fontSize: '24px', textAlign: 'center' }}>
                        {data.emoji}
                    </div>
                ) : (
                    '(emoji não configurado)'
                )}
                {data.targetMessage && (
                    <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                        Reagir à: {data.targetMessage}
                    </div>
                )}
            </div>
        </BaseNode>
    );
}
