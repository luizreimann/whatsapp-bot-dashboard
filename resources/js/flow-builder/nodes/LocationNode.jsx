import React from 'react';
import BaseNode from './BaseNode';

export default function LocationNode({ data, selected }) {
    return (
        <BaseNode
            selected={selected}
            icon="📍"
            title={data.label || 'Localização'}
            nodeType="location"
        >
            <div className="custom-node-preview">
                {data.latitude && data.longitude ? (
                    <>
                        <div>📍 {data.name || 'Localização'}</div>
                        <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                            {data.latitude}, {data.longitude}
                        </div>
                        {data.address && (
                            <div style={{ fontSize: '11px', marginTop: '4px' }}>
                                {data.address.substring(0, 50)}{data.address.length > 50 ? '...' : ''}
                            </div>
                        )}
                    </>
                ) : (
                    '(localização não configurada)'
                )}
            </div>
        </BaseNode>
    );
}
