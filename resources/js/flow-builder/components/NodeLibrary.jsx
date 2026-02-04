import React from 'react';

const nodeCategories = [
    {
        title: 'Controle',
        nodes: [
            {
                type: 'start',
                name: 'Início',
                description: 'Ponto de entrada do fluxo',
                icon: '▶️',
                color: '#22c55e',
            },
            {
                type: 'end',
                name: 'Fim',
                description: 'Finalizar conversa',
                icon: '⏹️',
                color: '#ef4444',
            },
        ],
    },
    {
        title: 'Comunicação',
        nodes: [
            {
                type: 'message',
                name: 'Mensagem',
                description: 'Enviar texto',
                icon: '💬',
                color: '#3b82f6',
            },
            {
                type: 'question',
                name: 'Pergunta',
                description: 'Capturar resposta',
                icon: '❓',
                color: '#8b5cf6',
            },
            {
                type: 'media',
                name: 'Mídia',
                description: 'Imagem, vídeo, áudio',
                icon: '📎',
                color: '#10b981',
            },
            {
                type: 'location',
                name: 'Localização',
                description: 'Enviar localização',
                icon: '📍',
                color: '#ef4444',
            },
            {
                type: 'contact',
                name: 'Contato',
                description: 'Enviar vCard',
                icon: '👤',
                color: '#6366f1',
            },
            {
                type: 'reaction',
                name: 'Reação',
                description: 'Reagir com emoji',
                icon: '💬',
                color: '#f472b6',
            },
        ],
    },
    {
        title: 'Lógica',
        nodes: [
            {
                type: 'condition',
                name: 'Condição',
                description: 'If/else',
                icon: '🔀',
                color: '#f59e0b',
            },
            {
                type: 'switch',
                name: 'Switch',
                description: 'Múltiplos casos',
                icon: '🎛️',
                color: '#f97316',
            },
            {
                type: 'random',
                name: 'Randomizar',
                description: 'Teste A/B',
                icon: '🎲',
                color: '#a855f7',
            },
            {
                type: 'businessHours',
                name: 'Horário',
                description: 'Horário comercial',
                icon: '📅',
                color: '#14b8a6',
            },
            {
                type: 'delay',
                name: 'Aguardar',
                description: 'Esperar tempo',
                icon: '⏱️',
                color: '#9ca3af',
            },
        ],
    },
    {
        title: 'Dados',
        nodes: [
            {
                type: 'variable',
                name: 'Variável',
                description: 'Definir/modificar',
                icon: '💾',
                color: '#0ea5e9',
            },
            {
                type: 'webhook',
                name: 'Webhook',
                description: 'API externa',
                icon: '📊',
                color: '#8b5cf6',
            },
        ],
    },
    {
        title: 'Ações',
        nodes: [
            {
                type: 'action',
                name: 'Ação',
                description: 'Salvar lead, tags',
                icon: '⚡',
                color: '#ec4899',
            },
            {
                type: 'integration',
                name: 'Integração',
                description: 'CRM, planilhas',
                icon: '🔗',
                color: '#06b6d4',
            },
            {
                type: 'transfer',
                name: 'Transferir',
                description: 'Atendimento humano',
                icon: '👤',
                color: '#f59e0b',
            },
        ],
    },
];

export default function NodeLibrary() {
    const onDragStart = (event, nodeType) => {
        event.dataTransfer.setData('application/reactflow', nodeType);
        event.dataTransfer.effectAllowed = 'move';
    };

    return (
        <aside className="flow-builder-sidebar">
            <div className="sidebar-header">
                <h3>Blocos</h3>
            </div>
            <div className="sidebar-content">
                {nodeCategories.map((category) => (
                    <div key={category.title} className="node-category">
                        <div className="node-category-title">{category.title}</div>
                        {category.nodes.map((node) => (
                            <div
                                key={node.type}
                                className="node-item"
                                draggable
                                onDragStart={(e) => onDragStart(e, node.type)}
                            >
                                <div
                                    className="node-item-icon"
                                    style={{ backgroundColor: `${node.color}20`, color: node.color }}
                                >
                                    {node.icon}
                                </div>
                                <div className="node-item-info">
                                    <div className="node-item-name">{node.name}</div>
                                    <div className="node-item-desc">{node.description}</div>
                                </div>
                            </div>
                        ))}
                    </div>
                ))}
            </div>
        </aside>
    );
}
