import React from 'react';
import { Handle, Position } from '@xyflow/react';

export default function BusinessHoursNode({ data, selected }) {
    return (
        <div className={`custom-node node-business-hours ${selected ? 'selected' : ''}`}>
            <Handle
                type="target"
                position={Position.Top}
                id="input"
            />
            
            <div className="custom-node-header">
                <div className="custom-node-icon">📅</div>
                <div className="custom-node-title">{data.label || 'Horário Comercial'}</div>
            </div>
            
            <div className="custom-node-body">
                <div className="custom-node-preview">
                    {data.schedule ? (
                        <>
                            <div style={{ fontSize: '11px' }}>
                                {data.schedule.startTime || '09:00'} - {data.schedule.endTime || '18:00'}
                            </div>
                            <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                                {data.schedule.days?.join(', ') || 'Seg-Sex'}
                            </div>
                        </>
                    ) : (
                        '(horário não configurado)'
                    )}
                </div>
                
                <div style={{ 
                    display: 'flex', 
                    justifyContent: 'space-between', 
                    marginTop: '8px',
                    fontSize: '10px',
                    color: 'var(--fb-text-muted)'
                }}>
                    <span>✅ Aberto</span>
                    <span>❌ Fechado</span>
                </div>
            </div>
            
            <Handle
                type="source"
                position={Position.Bottom}
                id="open"
                style={{ left: '30%' }}
            />
            <Handle
                type="source"
                position={Position.Bottom}
                id="closed"
                style={{ left: '70%' }}
            />
        </div>
    );
}
