import React from 'react';
import { Handle, Position } from '@xyflow/react';

export default function BaseNode({ 
    children, 
    icon, 
    title, 
    preview, 
    nodeType,
    showSourceHandle = true,
    showTargetHandle = true,
    sourceHandles = [{ id: 'output', position: Position.Bottom }],
    targetHandles = [{ id: 'input', position: Position.Top }],
    selected,
}) {
    return (
        <div className={`custom-node node-${nodeType} ${selected ? 'selected' : ''}`}>
            {showTargetHandle && targetHandles.map((handle) => (
                <Handle
                    key={handle.id}
                    type="target"
                    position={handle.position}
                    id={handle.id}
                    style={handle.style}
                />
            ))}
            
            <div className="custom-node-header">
                <div className="custom-node-icon">{icon}</div>
                <div className="custom-node-title">{title}</div>
            </div>
            
            {(preview || children) && (
                <div className="custom-node-body">
                    {preview && <div className="custom-node-preview">{preview}</div>}
                    {children}
                </div>
            )}
            
            {showSourceHandle && sourceHandles.map((handle) => (
                <Handle
                    key={handle.id}
                    type="source"
                    position={handle.position}
                    id={handle.id}
                    style={handle.style}
                />
            ))}
        </div>
    );
}
