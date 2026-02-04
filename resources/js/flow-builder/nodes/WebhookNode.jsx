import React from 'react';
import BaseNode from './BaseNode';

export default function WebhookNode({ data, selected }) {
    const methodColors = {
        GET: '#22c55e',
        POST: '#3b82f6',
        PUT: '#f59e0b',
        DELETE: '#ef4444',
    };

    return (
        <BaseNode
            selected={selected}
            icon="📊"
            title={data.label || 'Webhook'}
            nodeType="webhook"
        >
            <div className="custom-node-preview">
                {data.url ? (
                    <>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <span style={{ 
                                fontSize: '10px', 
                                fontWeight: 'bold',
                                color: methodColors[data.method] || '#3b82f6',
                                backgroundColor: 'var(--fb-bg-tertiary)',
                                padding: '2px 6px',
                                borderRadius: '4px',
                            }}>
                                {data.method || 'POST'}
                            </span>
                        </div>
                        <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px', wordBreak: 'break-all' }}>
                            {data.url.length > 40 ? data.url.substring(0, 40) + '...' : data.url}
                        </div>
                    </>
                ) : (
                    '(URL não configurada)'
                )}
            </div>
        </BaseNode>
    );
}
