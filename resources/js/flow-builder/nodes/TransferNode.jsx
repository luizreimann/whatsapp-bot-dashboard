import React from 'react';
import BaseNode from './BaseNode';

export default function TransferNode({ data, selected }) {
    return (
        <BaseNode
            selected={selected}
            icon="👤"
            title={data.label || 'Transferir'}
            nodeType="transfer"
        >
            <div className="custom-node-preview">
                {data.department || data.agent ? (
                    <>
                        <div>🎯 {data.department || 'Atendimento'}</div>
                        {data.agent && (
                            <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                                Agente: {data.agent}
                            </div>
                        )}
                        {data.message && (
                            <div style={{ fontSize: '11px', marginTop: '4px', fontStyle: 'italic' }}>
                                "{data.message.substring(0, 30)}{data.message.length > 30 ? '...' : ''}"
                            </div>
                        )}
                    </>
                ) : (
                    '(transferência não configurada)'
                )}
            </div>
        </BaseNode>
    );
}
