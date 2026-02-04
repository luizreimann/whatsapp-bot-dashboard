import React from 'react';
import BaseNode from './BaseNode';

export default function MediaNode({ data, selected }) {
    const mediaTypeLabels = {
        image: '🖼️ Imagem',
        video: '🎬 Vídeo',
        audio: '🎵 Áudio',
        document: '📄 Documento',
        sticker: '🎨 Sticker',
    };

    return (
        <BaseNode
            selected={selected}
            icon="📎"
            title={data.label || 'Mídia'}
            nodeType="media"
        >
            <div className="custom-node-preview">
                {data.mediaType ? (
                    <>
                        <div>{mediaTypeLabels[data.mediaType] || data.mediaType}</div>
                        {data.url && (
                            <div style={{ fontSize: '10px', color: 'var(--fb-text-muted)', marginTop: '4px' }}>
                                {data.url.length > 30 ? data.url.substring(0, 30) + '...' : data.url}
                            </div>
                        )}
                        {data.caption && (
                            <div style={{ fontSize: '11px', marginTop: '4px', fontStyle: 'italic' }}>
                                "{data.caption.substring(0, 40)}{data.caption.length > 40 ? '...' : ''}"
                            </div>
                        )}
                    </>
                ) : (
                    '(tipo não configurado)'
                )}
            </div>
        </BaseNode>
    );
}
