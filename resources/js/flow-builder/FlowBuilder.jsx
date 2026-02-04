import React, { useState, useCallback, useRef, useEffect } from 'react';
import {
    ReactFlow,
    ReactFlowProvider,
    Controls,
    Background,
    MiniMap,
    addEdge,
    useNodesState,
    useEdgesState,
    useReactFlow,
} from '@xyflow/react';
import '@xyflow/react/dist/style.css';

import NodeLibrary from './components/NodeLibrary';
import PropertiesPanel from './components/PropertiesPanel';
import { nodeTypes } from './nodes';
import { useFlowStore } from './store';

const initialNodes = [
    {
        id: 'start-1',
        type: 'start',
        position: { x: 250, y: 50 },
        data: { label: 'Início', trigger: 'any', keyword: '' },
    },
];

const initialEdges = [];

function FlowBuilderInner({ flux, tenantId, integrations, saveUrl, backUrl, initialFluxName }) {
    const reactFlowWrapper = useRef(null);
    const { screenToFlowPosition } = useReactFlow();
    
    const [nodes, setNodes, onNodesChange] = useNodesState(
        flux?.data?.nodes || initialNodes
    );
    const [edges, setEdges, onEdgesChange] = useEdgesState(
        flux?.data?.edges || initialEdges
    );
    
    const [selectedNodeId, setSelectedNodeId] = useState(null);
    
    // Derivar o nó selecionado do array nodes atual
    const selectedNode = selectedNodeId ? nodes.find(n => n.id === selectedNodeId) : null;
    const [flowName, setFlowName] = useState(initialFluxName || flux?.name || 'Novo Fluxo');
    const [isDarkMode, setIsDarkMode] = useState(() => {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark';
    });

    const toggleTheme = () => {
        const newTheme = isDarkMode ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('dashboard-theme', newTheme);
        setIsDarkMode(!isDarkMode);
    };
    const [saveStatus, setSaveStatus] = useState('saved');
    const [validationErrors, setValidationErrors] = useState([]);
    
    const { setIntegrations } = useFlowStore();
    
    useEffect(() => {
        setIntegrations(integrations);
    }, [integrations, setIntegrations]);

    const onConnect = useCallback(
        (params) => {
            setEdges((eds) => addEdge({ ...params, animated: true }, eds));
            setSaveStatus('unsaved');
        },
        [setEdges]
    );

    const onNodeClick = useCallback((event, node) => {
        setSelectedNodeId(node.id);
    }, []);

    const onPaneClick = useCallback(() => {
        setSelectedNodeId(null);
    }, []);

    const onDragOver = useCallback((event) => {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
    }, []);

    const onDrop = useCallback(
        (event) => {
            event.preventDefault();

            const type = event.dataTransfer.getData('application/reactflow');
            if (!type) return;

            const position = screenToFlowPosition({
                x: event.clientX,
                y: event.clientY,
            });

            const newNode = {
                id: `${type}-${Date.now()}`,
                type,
                position,
                data: getDefaultNodeData(type),
            };

            setNodes((nds) => nds.concat(newNode));
            setSaveStatus('unsaved');
        },
        [screenToFlowPosition, setNodes]
    );

    const updateNodeData = useCallback(
        (nodeId, newData) => {
            setNodes((nds) =>
                nds.map((node) => {
                    if (node.id === nodeId) {
                        return {
                            ...node,
                            data: { ...node.data, ...newData },
                        };
                    }
                    return node;
                })
            );
            setSaveStatus('unsaved');
        },
        [setNodes]
    );

    const deleteNode = useCallback(
        (nodeId) => {
            setNodes((nds) => nds.filter((node) => node.id !== nodeId));
            setEdges((eds) =>
                eds.filter((edge) => edge.source !== nodeId && edge.target !== nodeId)
            );
            setSelectedNodeId(null);
            setSaveStatus('unsaved');
        },
        [setNodes, setEdges]
    );

    const validateFlow = useCallback(() => {
        const errors = [];
        
        const startNodes = nodes.filter((n) => n.type === 'start');
        if (startNodes.length === 0) {
            errors.push('O fluxo precisa ter um nó de início');
        } else if (startNodes.length > 1) {
            errors.push('O fluxo pode ter apenas um nó de início');
        }

        const endNodes = nodes.filter((n) => n.type === 'end');
        if (endNodes.length === 0) {
            errors.push('O fluxo precisa ter pelo menos um nó de fim');
        }

        const connectedNodeIds = new Set();
        edges.forEach((edge) => {
            connectedNodeIds.add(edge.source);
            connectedNodeIds.add(edge.target);
        });

        const disconnectedNodes = nodes.filter(
            (node) => node.type !== 'start' && !connectedNodeIds.has(node.id)
        );
        if (disconnectedNodes.length > 0) {
            errors.push(`${disconnectedNodes.length} nó(s) não estão conectados`);
        }

        setValidationErrors(errors);
        return errors.length === 0;
    }, [nodes, edges]);

    const saveFlow = useCallback(async () => {
        if (!validateFlow()) {
            return;
        }

        setSaveStatus('saving');

        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'X-HTTP-Method-Override': 'PUT',
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    name: flowName,
                    data: {
                        nodes,
                        edges,
                        version: 1,
                    },
                }),
            });

            if (response.ok) {
                setSaveStatus('saved');
                setValidationErrors([]);
            } else {
                setSaveStatus('error');
            }
        } catch (error) {
            console.error('Error saving flow:', error);
            setSaveStatus('error');
        }
    }, [nodes, edges, flowName, saveUrl, validateFlow]);

    useEffect(() => {
        const handleKeyDown = (event) => {
            if ((event.metaKey || event.ctrlKey) && event.key === 's') {
                event.preventDefault();
                saveFlow();
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, [saveFlow]);

    return (
        <div className="flow-builder">
            <header className="flow-builder-header">
                <div className="flow-builder-header-left">
                    <a href={backUrl} className="back-button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M19 12H5M12 19l-7-7 7-7" />
                        </svg>
                        Voltar
                    </a>
                    <input
                        type="text"
                        className="flow-name-input"
                        value={flowName}
                        onChange={(e) => {
                            setFlowName(e.target.value);
                            setSaveStatus('unsaved');
                        }}
                        placeholder="Nome do fluxo"
                    />
                </div>
                <div className="flow-builder-header-right">
                    <span className={`save-status ${saveStatus}`}>
                        {saveStatus === 'saving' && '⏳ Salvando...'}
                        {saveStatus === 'saved' && '✓ Salvo'}
                        {saveStatus === 'unsaved' && '○ Não salvo'}
                        {saveStatus === 'error' && '✕ Erro ao salvar'}
                    </span>
                    <button className="btn btn-secondary btn-icon" onClick={toggleTheme} title="Alternar tema">
                        {isDarkMode ? (
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                <circle cx="12" cy="12" r="5" />
                                <line x1="12" y1="1" x2="12" y2="3" />
                                <line x1="12" y1="21" x2="12" y2="23" />
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                                <line x1="1" y1="12" x2="3" y2="12" />
                                <line x1="21" y1="12" x2="23" y2="12" />
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                            </svg>
                        ) : (
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                            </svg>
                        )}
                    </button>
                    <button className="btn btn-secondary" onClick={validateFlow}>
                        Validar
                    </button>
                    <button className="btn btn-primary" onClick={saveFlow}>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Salvar
                    </button>
                </div>
            </header>

            <div className="flow-builder-content">
                <NodeLibrary />

                <div className="flow-builder-canvas" ref={reactFlowWrapper}>
                    <ReactFlow
                        nodes={nodes}
                        edges={edges}
                        onNodesChange={onNodesChange}
                        onEdgesChange={onEdgesChange}
                        onConnect={onConnect}
                        onNodeClick={onNodeClick}
                        onPaneClick={onPaneClick}
                        onDragOver={onDragOver}
                        onDrop={onDrop}
                        nodeTypes={nodeTypes}
                        fitView
                        snapToGrid
                        snapGrid={[15, 15]}
                    >
                        <Controls />
                        <MiniMap 
                            nodeColor={(node) => {
                                switch (node.type) {
                                    case 'start': return '#22c55e';
                                    case 'end': return '#ef4444';
                                    case 'message': return '#3b82f6';
                                    case 'question': return '#8b5cf6';
                                    case 'condition': return '#f59e0b';
                                    case 'action': return '#ec4899';
                                    case 'integration': return '#06b6d4';
                                    case 'delay': return '#9ca3af';
                                    default: return '#64748b';
                                }
                            }}
                        />
                        <Background variant="dots" gap={20} size={1} color="#334155" />
                    </ReactFlow>

                    {validationErrors.length > 0 && (
                        <div className="validation-errors">
                            {validationErrors.map((error, index) => (
                                <div key={index}>⚠️ {error}</div>
                            ))}
                        </div>
                    )}
                </div>

                <PropertiesPanel
                    selectedNode={selectedNode}
                    onUpdateNode={updateNodeData}
                    onDeleteNode={deleteNode}
                    onClose={() => setSelectedNodeId(null)}
                />
            </div>
        </div>
    );
}

function getDefaultNodeData(type) {
    switch (type) {
        case 'start':
            return { label: 'Início', trigger: 'any', keyword: '' };
        case 'message':
            return { label: 'Mensagem', text: '', delay: 0 };
        case 'question':
            return {
                label: 'Pergunta',
                question: '',
                variableName: '',
                validationType: 'text',
                timeout: 300,
                maxRetries: 3,
            };
        case 'condition':
            return {
                label: 'Condição',
                variable: '',
                operator: 'equals',
                value: '',
            };
        case 'switch':
            return {
                label: 'Switch',
                variable: '',
                cases: [],
            };
        case 'action':
            return {
                label: 'Ação',
                actionType: 'save_lead',
                config: {},
            };
        case 'integration':
            return {
                label: 'Integração',
                integrationId: null,
                provider: '',
                action: '',
                fieldMapping: {},
            };
        case 'delay':
            return {
                label: 'Aguardar',
                duration: 60,
                unit: 'seconds',
            };
        case 'end':
            return {
                label: 'Fim',
                message: '',
                markAsCompleted: true,
            };
        case 'media':
            return {
                label: 'Mídia',
                mediaType: 'image',
                url: '',
                caption: '',
            };
        case 'location':
            return {
                label: 'Localização',
                name: '',
                latitude: '',
                longitude: '',
                address: '',
            };
        case 'contact':
            return {
                label: 'Contato',
                contactName: '',
                phone: '',
                email: '',
                organization: '',
            };
        case 'reaction':
            return {
                label: 'Reação',
                emoji: '👍',
                targetMessage: 'last_received',
            };
        case 'random':
            return {
                label: 'Randomizar',
                paths: [],
            };
        case 'businessHours':
            return {
                label: 'Horário Comercial',
                schedule: {
                    startTime: '09:00',
                    endTime: '18:00',
                    days: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex'],
                    timezone: 'America/Sao_Paulo',
                },
            };
        case 'variable':
            return {
                label: 'Variável',
                variableName: '',
                operation: 'set',
                value: '',
            };
        case 'webhook':
            return {
                label: 'Webhook',
                method: 'POST',
                url: '',
                headers: [],
                body: '',
                responseVariable: '',
            };
        case 'transfer':
            return {
                label: 'Transferir',
                department: '',
                agent: '',
                message: '',
                priority: 'normal',
            };
        default:
            return { label: type };
    }
}

export default function FlowBuilder(props) {
    return (
        <ReactFlowProvider>
            <FlowBuilderInner {...props} />
        </ReactFlowProvider>
    );
}
