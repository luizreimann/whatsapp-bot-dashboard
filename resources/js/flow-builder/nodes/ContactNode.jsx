import React from 'react';
import BaseNode from './BaseNode';

export default function ContactNode({ data, selected }) {
    return (
        <BaseNode
            selected={selected}
            icon="👤"
            title={data.label || 'Contato'}
            nodeType="contact"
        >
            <div className="custom-node-preview">
                {data.contactName ? (
                    <>
                        <div>👤 {data.contactName}</div>
                        {data.phone && (
                            <div style={{ fontSize: '11px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                                📞 {data.phone}
                            </div>
                        )}
                    </>
                ) : (
                    '(contato não configurado)'
                )}
            </div>
        </BaseNode>
    );
}
