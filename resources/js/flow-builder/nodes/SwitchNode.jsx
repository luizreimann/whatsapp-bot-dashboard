import React from 'react';
import { Handle, Position } from '@xyflow/react';

export default function SwitchNode({ data, selected }) {
    const cases = data.cases || [];
    
    return (
        <div className={`custom-node node-switch ${selected ? 'selected' : ''}`}>
            <Handle
                type="target"
                position={Position.Top}
                id="input"
            />
            
            <div className="custom-node-header">
                <div className="custom-node-icon">🔀</div>
                <div className="custom-node-title">{data.label || 'Switch'}</div>
            </div>
            
            <div className="custom-node-body">
                <div className="custom-node-preview">
                    {data.variable ? (
                        <>
                            <div style={{ marginBottom: '6px', fontWeight: 500 }}>
                                {`{{${data.variable}}}`}
                            </div>
                            {cases.length > 0 ? (
                                <div style={{ fontSize: '11px', color: 'var(--fb-text-muted)' }}>
                                    {cases.length} caso(s) + padrão
                                </div>
                            ) : (
                                <div style={{ fontSize: '11px', color: 'var(--fb-text-muted)' }}>
                                    Nenhum caso definido
                                </div>
                            )}
                        </>
                    ) : (
                        '(variável não configurada)'
                    )}
                </div>
                
                {/* Saídas para cada case */}
                <div className="switch-outputs" style={{ marginTop: '10px' }}>
                    {cases.map((caseItem, index) => (
                        <div 
                            key={index} 
                            className="switch-output-item"
                            style={{
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'space-between',
                                padding: '4px 8px',
                                marginBottom: '4px',
                                backgroundColor: 'var(--fb-bg-tertiary)',
                                borderRadius: '6px',
                                fontSize: '11px',
                            }}
                        >
                            <span style={{ color: 'var(--fb-accent)' }}>"{caseItem.value}"</span>
                        </div>
                    ))}
                    <div 
                        className="switch-output-item"
                        style={{
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'space-between',
                            padding: '4px 8px',
                            backgroundColor: 'var(--fb-bg-tertiary)',
                            borderRadius: '6px',
                            fontSize: '11px',
                        }}
                    >
                        <span style={{ color: 'var(--fb-text-muted)', fontStyle: 'italic' }}>padrão</span>
                    </div>
                </div>
            </div>
            
            {/* Handles para cada case + default */}
            {cases.map((caseItem, index) => (
                <Handle
                    key={`case-${index}`}
                    type="source"
                    position={Position.Bottom}
                    id={`case-${index}`}
                    style={{ 
                        left: `${((index + 1) / (cases.length + 2)) * 100}%`,
                    }}
                />
            ))}
            <Handle
                type="source"
                position={Position.Bottom}
                id="default"
                style={{ 
                    left: `${((cases.length + 1) / (cases.length + 2)) * 100}%`,
                }}
            />
        </div>
    );
}
