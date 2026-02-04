import React from 'react';
import { Handle, Position } from '@xyflow/react';

export default function RandomNode({ data, selected }) {
    const paths = data.paths || [];
    
    return (
        <div className={`custom-node node-random ${selected ? 'selected' : ''}`}>
            <Handle
                type="target"
                position={Position.Top}
                id="input"
            />
            
            <div className="custom-node-header">
                <div className="custom-node-icon">🎲</div>
                <div className="custom-node-title">{data.label || 'Randomizar'}</div>
            </div>
            
            <div className="custom-node-body">
                <div className="custom-node-preview">
                    {paths.length > 0 ? (
                        <>
                            <div style={{ marginBottom: '6px' }}>
                                {paths.length} caminhos
                            </div>
                            <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)' }}>
                                {paths.map((p, i) => `${p.weight || Math.floor(100/paths.length)}%`).join(' / ')}
                            </div>
                        </>
                    ) : (
                        '(nenhum caminho definido)'
                    )}
                </div>
            </div>
            
            {paths.map((path, index) => (
                <Handle
                    key={`path-${index}`}
                    type="source"
                    position={Position.Bottom}
                    id={`path-${index}`}
                    style={{ 
                        left: `${((index + 1) / (paths.length + 1)) * 100}%`,
                    }}
                />
            ))}
            {paths.length === 0 && (
                <Handle
                    type="source"
                    position={Position.Bottom}
                    id="default"
                />
            )}
        </div>
    );
}
