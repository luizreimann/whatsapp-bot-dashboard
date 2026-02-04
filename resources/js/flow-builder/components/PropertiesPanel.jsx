import React from 'react';
import { useFlowStore } from '../store';

const handleInputEvent = (e) => {
    e.stopPropagation();
};

export default function PropertiesPanel({ selectedNode, onUpdateNode, onDeleteNode, onClose }) {
    const { integrations } = useFlowStore();

    if (!selectedNode) {
        return (
            <aside className="flow-builder-properties">
                <div className="properties-header">
                    <h3>Propriedades</h3>
                </div>
                <div className="properties-empty">
                    <div className="properties-empty-icon">📋</div>
                    <p>Selecione um bloco para editar suas propriedades</p>
                </div>
            </aside>
        );
    }

    const handleChange = (field, value) => {
        onUpdateNode(selectedNode.id, { [field]: value });
    };

    const renderFields = () => {
        switch (selectedNode.type) {
            case 'start':
                return <StartNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'message':
                return <MessageNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'question':
                return <QuestionNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'condition':
                return <ConditionNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'switch':
                return <SwitchNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'action':
                return <ActionNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'integration':
                return <IntegrationNodeFields data={selectedNode.data} onChange={handleChange} integrations={integrations} />;
            case 'delay':
                return <DelayNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'end':
                return <EndNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'media':
                return <MediaNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'location':
                return <LocationNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'contact':
                return <ContactNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'reaction':
                return <ReactionNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'random':
                return <RandomNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'businessHours':
                return <BusinessHoursNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'variable':
                return <VariableNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'webhook':
                return <WebhookNodeFields data={selectedNode.data} onChange={handleChange} />;
            case 'transfer':
                return <TransferNodeFields data={selectedNode.data} onChange={handleChange} />;
            default:
                return <p>Tipo de nó não suportado</p>;
        }
    };

    return (
        <aside className="flow-builder-properties">
            <div className="properties-header">
                <h3>{selectedNode.data.label || selectedNode.type}</h3>
                <button className="properties-close" onClick={onClose}>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div className="properties-content">
                <div className="form-group">
                    <label className="form-label">Nome do bloco</label>
                    <input
                        type="text"
                        className="form-input"
                        value={selectedNode.data.label || ''}
                        onChange={(e) => handleChange('label', e.target.value)}
                        onKeyDown={handleInputEvent}
                        onMouseDown={handleInputEvent}
                        onClick={handleInputEvent}
                        placeholder="Nome do bloco"
                    />
                </div>

                {renderFields()}

                {selectedNode.type !== 'start' && (
                    <div style={{ marginTop: '24px', paddingTop: '16px', borderTop: '1px solid var(--fb-border)' }}>
                        <button
                            className="btn btn-danger"
                            style={{ width: '100%' }}
                            onClick={() => onDeleteNode(selectedNode.id)}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                            </svg>
                            Excluir bloco
                        </button>
                    </div>
                )}
            </div>
        </aside>
    );
}

function StartNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Gatilho</label>
                <select
                    className="form-select"
                    value={data.trigger || 'any'}
                    onChange={(e) => onChange('trigger', e.target.value)}
                >
                    <option value="any">Qualquer mensagem</option>
                    <option value="keyword">Palavra-chave</option>
                </select>
            </div>
            {data.trigger === 'keyword' && (
                <div className="form-group">
                    <label className="form-label">Palavra-chave</label>
                    <input
                        type="text"
                        className="form-input"
                        value={data.keyword || ''}
                        onChange={(e) => onChange('keyword', e.target.value)}
                        onKeyDown={handleInputEvent}
                        onMouseDown={handleInputEvent}
                        onClick={handleInputEvent}
                        placeholder="Ex: oi, olá, começar"
                    />
                    <span className="form-hint">Separe múltiplas palavras com vírgula</span>
                </div>
            )}
        </>
    );
}

function MessageNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Mensagem</label>
                <textarea
                    className="form-textarea"
                    value={data.text || ''}
                    onChange={(e) => onChange('text', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Digite a mensagem..."
                />
                <span className="form-hint">Use {'{{variavel}}'} para inserir dados capturados</span>
            </div>
            <div className="form-group">
                <label className="form-label">Delay (segundos)</label>
                <input
                    type="number"
                    className="form-input"
                    value={data.delay || 0}
                    onChange={(e) => onChange('delay', parseInt(e.target.value) || 0)}
                    min="0"
                    max="60"
                />
                <span className="form-hint">Aguardar antes de enviar (simula digitação)</span>
            </div>
        </>
    );
}

function QuestionNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Pergunta</label>
                <textarea
                    className="form-textarea"
                    value={data.question || ''}
                    onChange={(e) => onChange('question', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Digite a pergunta..."
                />
            </div>
            <div className="form-group">
                <label className="form-label">Salvar resposta em</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.variableName || ''}
                    onChange={(e) => onChange('variableName', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="nome_da_variavel"
                />
                <span className="form-hint">Use depois como {'{{nome_da_variavel}}'}</span>
            </div>
            <div className="form-group">
                <label className="form-label">Tipo de validação</label>
                <select
                    className="form-select"
                    value={data.validationType || 'text'}
                    onChange={(e) => onChange('validationType', e.target.value)}
                >
                    <option value="text">Texto livre</option>
                    <option value="number">Número</option>
                    <option value="email">E-mail</option>
                    <option value="phone">Telefone</option>
                </select>
            </div>
            <div className="form-group">
                <label className="form-label">Timeout (segundos)</label>
                <input
                    type="number"
                    className="form-input"
                    value={data.timeout || 300}
                    onChange={(e) => onChange('timeout', parseInt(e.target.value) || 300)}
                    min="30"
                    max="3600"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Máximo de tentativas</label>
                <input
                    type="number"
                    className="form-input"
                    value={data.maxRetries || 3}
                    onChange={(e) => onChange('maxRetries', parseInt(e.target.value) || 3)}
                    min="1"
                    max="5"
                />
            </div>
        </>
    );
}

function ConditionNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Variável</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.variable || ''}
                    onChange={(e) => onChange('variable', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="nome_da_variavel"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Operador</label>
                <select
                    className="form-select"
                    value={data.operator || 'equals'}
                    onChange={(e) => onChange('operator', e.target.value)}
                >
                    <option value="equals">Igual a</option>
                    <option value="not_equals">Diferente de</option>
                    <option value="contains">Contém</option>
                    <option value="not_contains">Não contém</option>
                    <option value="greater">Maior que</option>
                    <option value="less">Menor que</option>
                    <option value="starts_with">Começa com</option>
                    <option value="ends_with">Termina com</option>
                </select>
            </div>
            <div className="form-group">
                <label className="form-label">Valor</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.value || ''}
                    onChange={(e) => onChange('value', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Valor para comparar"
                />
            </div>
        </>
    );
}

function SwitchNodeFields({ data, onChange }) {
    const cases = data.cases || [];

    const addCase = () => {
        const newCases = [...cases, { value: '', label: `Caso ${cases.length + 1}` }];
        onChange('cases', newCases);
    };

    const updateCase = (index, field, value) => {
        const newCases = cases.map((c, i) => 
            i === index ? { ...c, [field]: value } : c
        );
        onChange('cases', newCases);
    };

    const removeCase = (index) => {
        const newCases = cases.filter((_, i) => i !== index);
        onChange('cases', newCases);
    };

    return (
        <>
            <div className="form-group">
                <label className="form-label">Variável</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.variable || ''}
                    onChange={(e) => onChange('variable', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="nome_da_variavel"
                />
                <span className="form-hint">A variável que será avaliada</span>
            </div>

            <div className="form-group">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' }}>
                    <label className="form-label" style={{ margin: 0 }}>Casos</label>
                    <button
                        type="button"
                        onClick={addCase}
                        style={{
                            padding: '4px 10px',
                            fontSize: '12px',
                            backgroundColor: 'var(--fb-accent)',
                            color: 'white',
                            border: 'none',
                            borderRadius: '6px',
                            cursor: 'pointer',
                        }}
                    >
                        + Adicionar
                    </button>
                </div>

                {cases.length === 0 ? (
                    <div style={{ 
                        padding: '12px', 
                        backgroundColor: 'var(--fb-bg-tertiary)', 
                        borderRadius: '8px', 
                        fontSize: '12px', 
                        color: 'var(--fb-text-muted)',
                        textAlign: 'center'
                    }}>
                        Nenhum caso definido. Clique em "+ Adicionar" para criar.
                    </div>
                ) : (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                        {cases.map((caseItem, index) => (
                            <div 
                                key={index}
                                style={{
                                    display: 'flex',
                                    gap: '8px',
                                    alignItems: 'center',
                                    padding: '8px',
                                    backgroundColor: 'var(--fb-bg-tertiary)',
                                    borderRadius: '8px',
                                }}
                            >
                                <span style={{ fontSize: '12px', color: 'var(--fb-text-muted)', minWidth: '20px' }}>
                                    {index + 1}.
                                </span>
                                <input
                                    type="text"
                                    className="form-input"
                                    value={caseItem.value}
                                    onChange={(e) => updateCase(index, 'value', e.target.value)}
                                    onKeyDown={handleInputEvent}
                                    onMouseDown={handleInputEvent}
                                    onClick={handleInputEvent}
                                    placeholder="Valor"
                                    style={{ flex: 1, padding: '8px 10px' }}
                                />
                                <button
                                    type="button"
                                    onClick={() => removeCase(index)}
                                    style={{
                                        padding: '6px 8px',
                                        backgroundColor: 'transparent',
                                        color: 'var(--fb-danger)',
                                        border: 'none',
                                        borderRadius: '4px',
                                        cursor: 'pointer',
                                        fontSize: '14px',
                                    }}
                                    title="Remover caso"
                                >
                                    ✕
                                </button>
                            </div>
                        ))}
                    </div>
                )}
                <span className="form-hint" style={{ marginTop: '8px', display: 'block' }}>
                    Cada caso cria uma saída. Valores não listados vão para "padrão".
                </span>
            </div>
        </>
    );
}

function ActionNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Tipo de ação</label>
                <select
                    className="form-select"
                    value={data.actionType || 'save_lead'}
                    onChange={(e) => onChange('actionType', e.target.value)}
                >
                    <option value="save_lead">Salvar lead</option>
                    <option value="update_lead">Atualizar lead</option>
                    <option value="add_tag">Adicionar tag</option>
                    <option value="remove_tag">Remover tag</option>
                </select>
            </div>
            {data.actionType === 'add_tag' && (
                <div className="form-group">
                    <label className="form-label">Tag</label>
                    <input
                        type="text"
                        className="form-input"
                        value={data.config?.tag || ''}
                        onChange={(e) => onChange('config', { ...data.config, tag: e.target.value })}
                        onKeyDown={handleInputEvent}
                        onMouseDown={handleInputEvent}
                        onClick={handleInputEvent}
                        placeholder="Nome da tag"
                    />
                </div>
            )}
        </>
    );
}

function IntegrationNodeFields({ data, onChange, integrations }) {
    const connectedIntegrations = integrations.filter((i) => i.status === 'connected');
    const selectedIntegration = integrations.find((i) => i.id === data.integrationId);

    return (
        <>
            <div className="form-group">
                <label className="form-label">Integração</label>
                {connectedIntegrations.length === 0 ? (
                    <div style={{ padding: '12px', backgroundColor: 'var(--fb-bg-tertiary)', borderRadius: '6px', fontSize: '12px', color: 'var(--fb-text-muted)' }}>
                        Nenhuma integração conectada.
                        <a href="/dashboard/integrations" style={{ color: 'var(--fb-accent)', marginLeft: '4px' }}>
                            Conectar agora →
                        </a>
                    </div>
                ) : (
                    <select
                        className="form-select"
                        value={data.integrationId || ''}
                        onChange={(e) => {
                            const integration = integrations.find((i) => i.id === parseInt(e.target.value));
                            onChange('integrationId', parseInt(e.target.value));
                            onChange('provider', integration?.provider || '');
                        }}
                    >
                        <option value="">Selecione...</option>
                        {connectedIntegrations.map((integration) => (
                            <option key={integration.id} value={integration.id}>
                                {integration.name} ({integration.provider})
                            </option>
                        ))}
                    </select>
                )}
            </div>
            {selectedIntegration && (
                <div className="form-group">
                    <label className="form-label">Ação</label>
                    <select
                        className="form-select"
                        value={data.action || ''}
                        onChange={(e) => onChange('action', e.target.value)}
                    >
                        <option value="">Selecione...</option>
                        <option value="sync_lead">Sincronizar lead</option>
                        <option value="create_deal">Criar negócio</option>
                        <option value="add_note">Adicionar nota</option>
                    </select>
                </div>
            )}
        </>
    );
}

function DelayNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Duração</label>
                <input
                    type="number"
                    className="form-input"
                    value={data.duration || 60}
                    onChange={(e) => onChange('duration', parseInt(e.target.value) || 60)}
                    min="1"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Unidade</label>
                <select
                    className="form-select"
                    value={data.unit || 'seconds'}
                    onChange={(e) => onChange('unit', e.target.value)}
                >
                    <option value="seconds">Segundos</option>
                    <option value="minutes">Minutos</option>
                    <option value="hours">Horas</option>
                </select>
            </div>
        </>
    );
}

function EndNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Mensagem de encerramento</label>
                <textarea
                    className="form-textarea"
                    value={data.message || ''}
                    onChange={(e) => onChange('message', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Mensagem final (opcional)"
                />
            </div>
            <div className="form-group">
                <label style={{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer' }}>
                    <input
                        type="checkbox"
                        checked={data.markAsCompleted !== false}
                        onChange={(e) => onChange('markAsCompleted', e.target.checked)}
                    />
                    <span className="form-label" style={{ margin: 0 }}>Marcar como concluído</span>
                </label>
            </div>
        </>
    );
}

function MediaNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Tipo de mídia</label>
                <select
                    className="form-select"
                    value={data.mediaType || 'image'}
                    onChange={(e) => onChange('mediaType', e.target.value)}
                >
                    <option value="image">🖼️ Imagem</option>
                    <option value="video">🎬 Vídeo</option>
                    <option value="audio">🎵 Áudio</option>
                    <option value="document">📄 Documento</option>
                    <option value="sticker">🎨 Sticker</option>
                </select>
            </div>
            <div className="form-group">
                <label className="form-label">URL ou caminho do arquivo</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.url || ''}
                    onChange={(e) => onChange('url', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="https://... ou /path/to/file"
                />
                <span className="form-hint">URL pública ou caminho local do arquivo</span>
            </div>
            <div className="form-group">
                <label className="form-label">Legenda (opcional)</label>
                <textarea
                    className="form-textarea"
                    value={data.caption || ''}
                    onChange={(e) => onChange('caption', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Legenda da mídia..."
                />
            </div>
        </>
    );
}

function LocationNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Nome do local</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.name || ''}
                    onChange={(e) => onChange('name', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Ex: Escritório Central"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Latitude</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.latitude || ''}
                    onChange={(e) => onChange('latitude', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="-23.550520"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Longitude</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.longitude || ''}
                    onChange={(e) => onChange('longitude', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="-46.633309"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Endereço (opcional)</label>
                <textarea
                    className="form-textarea"
                    value={data.address || ''}
                    onChange={(e) => onChange('address', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Rua, número, cidade..."
                />
            </div>
        </>
    );
}

function ContactNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Nome do contato</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.contactName || ''}
                    onChange={(e) => onChange('contactName', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Nome completo"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Telefone</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.phone || ''}
                    onChange={(e) => onChange('phone', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="+55 11 99999-9999"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Email (opcional)</label>
                <input
                    type="email"
                    className="form-input"
                    value={data.email || ''}
                    onChange={(e) => onChange('email', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="email@exemplo.com"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Organização (opcional)</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.organization || ''}
                    onChange={(e) => onChange('organization', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Nome da empresa"
                />
            </div>
        </>
    );
}

function ReactionNodeFields({ data, onChange }) {
    const commonEmojis = ['👍', '❤️', '😂', '😮', '😢', '🙏', '🔥', '🎉', '👏', '💯'];
    
    return (
        <>
            <div className="form-group">
                <label className="form-label">Emoji</label>
                <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px', marginBottom: '8px' }}>
                    {commonEmojis.map((emoji) => (
                        <button
                            key={emoji}
                            type="button"
                            onClick={() => onChange('emoji', emoji)}
                            style={{
                                fontSize: '20px',
                                padding: '8px',
                                border: data.emoji === emoji ? '2px solid var(--fb-accent)' : '1px solid var(--fb-border)',
                                borderRadius: '8px',
                                backgroundColor: 'var(--fb-bg-tertiary)',
                                cursor: 'pointer',
                            }}
                        >
                            {emoji}
                        </button>
                    ))}
                </div>
                <input
                    type="text"
                    className="form-input"
                    value={data.emoji || ''}
                    onChange={(e) => onChange('emoji', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Ou digite um emoji"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Reagir à mensagem</label>
                <select
                    className="form-select"
                    value={data.targetMessage || 'last_received'}
                    onChange={(e) => onChange('targetMessage', e.target.value)}
                >
                    <option value="last_received">Última mensagem recebida</option>
                    <option value="last_sent">Última mensagem enviada</option>
                </select>
            </div>
        </>
    );
}

function RandomNodeFields({ data, onChange }) {
    const paths = data.paths || [];

    const addPath = () => {
        const newPaths = [...paths, { name: `Caminho ${paths.length + 1}`, weight: Math.floor(100 / (paths.length + 1)) }];
        onChange('paths', newPaths);
    };

    const updatePath = (index, field, value) => {
        const newPaths = paths.map((p, i) => 
            i === index ? { ...p, [field]: value } : p
        );
        onChange('paths', newPaths);
    };

    const removePath = (index) => {
        const newPaths = paths.filter((_, i) => i !== index);
        onChange('paths', newPaths);
    };

    return (
        <>
            <div className="form-group">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' }}>
                    <label className="form-label" style={{ margin: 0 }}>Caminhos</label>
                    <button
                        type="button"
                        onClick={addPath}
                        style={{
                            padding: '4px 10px',
                            fontSize: '12px',
                            backgroundColor: 'var(--fb-accent)',
                            color: 'white',
                            border: 'none',
                            borderRadius: '6px',
                            cursor: 'pointer',
                        }}
                    >
                        + Adicionar
                    </button>
                </div>

                {paths.length === 0 ? (
                    <div style={{ 
                        padding: '12px', 
                        backgroundColor: 'var(--fb-bg-tertiary)', 
                        borderRadius: '8px', 
                        fontSize: '12px', 
                        color: 'var(--fb-text-muted)',
                        textAlign: 'center'
                    }}>
                        Adicione caminhos para teste A/B
                    </div>
                ) : (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                        {paths.map((path, index) => (
                            <div 
                                key={index}
                                style={{
                                    display: 'flex',
                                    gap: '8px',
                                    alignItems: 'center',
                                    padding: '8px',
                                    backgroundColor: 'var(--fb-bg-tertiary)',
                                    borderRadius: '8px',
                                }}
                            >
                                <input
                                    type="text"
                                    className="form-input"
                                    value={path.name || ''}
                                    onChange={(e) => updatePath(index, 'name', e.target.value)}
                                    onKeyDown={handleInputEvent}
                                    onMouseDown={handleInputEvent}
                                    onClick={handleInputEvent}
                                    placeholder="Nome"
                                    style={{ flex: 1, padding: '8px 10px' }}
                                />
                                <input
                                    type="number"
                                    className="form-input"
                                    value={path.weight || 50}
                                    onChange={(e) => updatePath(index, 'weight', parseInt(e.target.value) || 0)}
                                    onKeyDown={handleInputEvent}
                                    onMouseDown={handleInputEvent}
                                    onClick={handleInputEvent}
                                    min="0"
                                    max="100"
                                    style={{ width: '60px', padding: '8px 10px' }}
                                />
                                <span style={{ fontSize: '12px', color: 'var(--fb-text-muted)' }}>%</span>
                                <button
                                    type="button"
                                    onClick={() => removePath(index)}
                                    style={{
                                        padding: '6px 8px',
                                        backgroundColor: 'transparent',
                                        color: 'var(--fb-danger)',
                                        border: 'none',
                                        borderRadius: '4px',
                                        cursor: 'pointer',
                                        fontSize: '14px',
                                    }}
                                >
                                    ✕
                                </button>
                            </div>
                        ))}
                    </div>
                )}
                <span className="form-hint" style={{ marginTop: '8px', display: 'block' }}>
                    Defina o peso (%) de cada caminho. Total não precisa ser 100%.
                </span>
            </div>
        </>
    );
}

function BusinessHoursNodeFields({ data, onChange }) {
    const schedule = data.schedule || {};
    const days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    const selectedDays = schedule.days || ['Seg', 'Ter', 'Qua', 'Qui', 'Sex'];

    const toggleDay = (day) => {
        const newDays = selectedDays.includes(day)
            ? selectedDays.filter(d => d !== day)
            : [...selectedDays, day];
        onChange('schedule', { ...schedule, days: newDays });
    };

    return (
        <>
            <div className="form-group">
                <label className="form-label">Horário de início</label>
                <input
                    type="time"
                    className="form-input"
                    value={schedule.startTime || '09:00'}
                    onChange={(e) => onChange('schedule', { ...schedule, startTime: e.target.value })}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                />
            </div>
            <div className="form-group">
                <label className="form-label">Horário de término</label>
                <input
                    type="time"
                    className="form-input"
                    value={schedule.endTime || '18:00'}
                    onChange={(e) => onChange('schedule', { ...schedule, endTime: e.target.value })}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                />
            </div>
            <div className="form-group">
                <label className="form-label">Dias da semana</label>
                <div style={{ display: 'flex', flexWrap: 'wrap', gap: '6px' }}>
                    {days.map((day) => (
                        <button
                            key={day}
                            type="button"
                            onClick={() => toggleDay(day)}
                            style={{
                                padding: '6px 10px',
                                fontSize: '12px',
                                border: selectedDays.includes(day) ? '2px solid var(--fb-accent)' : '1px solid var(--fb-border)',
                                borderRadius: '6px',
                                backgroundColor: selectedDays.includes(day) ? 'var(--fb-accent)' : 'var(--fb-bg-tertiary)',
                                color: selectedDays.includes(day) ? 'white' : 'var(--fb-text)',
                                cursor: 'pointer',
                            }}
                        >
                            {day}
                        </button>
                    ))}
                </div>
            </div>
            <div className="form-group">
                <label className="form-label">Fuso horário</label>
                <select
                    className="form-select"
                    value={schedule.timezone || 'America/Sao_Paulo'}
                    onChange={(e) => onChange('schedule', { ...schedule, timezone: e.target.value })}
                >
                    <option value="America/Sao_Paulo">São Paulo (GMT-3)</option>
                    <option value="America/Manaus">Manaus (GMT-4)</option>
                    <option value="America/Recife">Recife (GMT-3)</option>
                    <option value="America/Fortaleza">Fortaleza (GMT-3)</option>
                </select>
            </div>
        </>
    );
}

function VariableNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Nome da variável</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.variableName || ''}
                    onChange={(e) => onChange('variableName', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="nome_da_variavel"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Operação</label>
                <select
                    className="form-select"
                    value={data.operation || 'set'}
                    onChange={(e) => onChange('operation', e.target.value)}
                >
                    <option value="set">Definir valor</option>
                    <option value="append">Concatenar texto</option>
                    <option value="increment">Incrementar (+1)</option>
                    <option value="decrement">Decrementar (-1)</option>
                </select>
            </div>
            {(data.operation === 'set' || data.operation === 'append' || !data.operation) && (
                <div className="form-group">
                    <label className="form-label">Valor</label>
                    <input
                        type="text"
                        className="form-input"
                        value={data.value || ''}
                        onChange={(e) => onChange('value', e.target.value)}
                        onKeyDown={handleInputEvent}
                        onMouseDown={handleInputEvent}
                        onClick={handleInputEvent}
                        placeholder="Valor ou {{outra_variavel}}"
                    />
                    <span className="form-hint">Use {'{{variavel}}'} para referenciar outras variáveis</span>
                </div>
            )}
        </>
    );
}

function WebhookNodeFields({ data, onChange }) {
    const headers = data.headers || [];

    const addHeader = () => {
        onChange('headers', [...headers, { key: '', value: '' }]);
    };

    const updateHeader = (index, field, value) => {
        const newHeaders = headers.map((h, i) => 
            i === index ? { ...h, [field]: value } : h
        );
        onChange('headers', newHeaders);
    };

    const removeHeader = (index) => {
        onChange('headers', headers.filter((_, i) => i !== index));
    };

    return (
        <>
            <div className="form-group">
                <label className="form-label">Método HTTP</label>
                <select
                    className="form-select"
                    value={data.method || 'POST'}
                    onChange={(e) => onChange('method', e.target.value)}
                >
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </div>
            <div className="form-group">
                <label className="form-label">URL</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.url || ''}
                    onChange={(e) => onChange('url', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="https://api.exemplo.com/webhook"
                />
            </div>
            <div className="form-group">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' }}>
                    <label className="form-label" style={{ margin: 0 }}>Headers</label>
                    <button
                        type="button"
                        onClick={addHeader}
                        style={{
                            padding: '4px 10px',
                            fontSize: '12px',
                            backgroundColor: 'var(--fb-accent)',
                            color: 'white',
                            border: 'none',
                            borderRadius: '6px',
                            cursor: 'pointer',
                        }}
                    >
                        + Adicionar
                    </button>
                </div>
                {headers.map((header, index) => (
                    <div key={index} style={{ display: 'flex', gap: '8px', marginBottom: '8px' }}>
                        <input
                            type="text"
                            className="form-input"
                            value={header.key}
                            onChange={(e) => updateHeader(index, 'key', e.target.value)}
                            onKeyDown={handleInputEvent}
                            onMouseDown={handleInputEvent}
                            onClick={handleInputEvent}
                            placeholder="Header"
                            style={{ flex: 1 }}
                        />
                        <input
                            type="text"
                            className="form-input"
                            value={header.value}
                            onChange={(e) => updateHeader(index, 'value', e.target.value)}
                            onKeyDown={handleInputEvent}
                            onMouseDown={handleInputEvent}
                            onClick={handleInputEvent}
                            placeholder="Valor"
                            style={{ flex: 1 }}
                        />
                        <button
                            type="button"
                            onClick={() => removeHeader(index)}
                            style={{
                                padding: '6px 8px',
                                backgroundColor: 'transparent',
                                color: 'var(--fb-danger)',
                                border: 'none',
                                cursor: 'pointer',
                            }}
                        >
                            ✕
                        </button>
                    </div>
                ))}
            </div>
            {(data.method === 'POST' || data.method === 'PUT') && (
                <div className="form-group">
                    <label className="form-label">Body (JSON)</label>
                    <textarea
                        className="form-textarea"
                        value={data.body || ''}
                        onChange={(e) => onChange('body', e.target.value)}
                        onKeyDown={handleInputEvent}
                        onMouseDown={handleInputEvent}
                        onClick={handleInputEvent}
                        placeholder='{"nome": "{{nome}}", "telefone": "{{telefone}}"}'
                        style={{ fontFamily: 'monospace', fontSize: '11px' }}
                    />
                    <span className="form-hint">Use {'{{variavel}}'} para inserir dados capturados</span>
                </div>
            )}
            <div className="form-group">
                <label className="form-label">Salvar resposta em</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.responseVariable || ''}
                    onChange={(e) => onChange('responseVariable', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="resposta_webhook"
                />
                <span className="form-hint">Variável para armazenar a resposta da API</span>
            </div>
        </>
    );
}

function TransferNodeFields({ data, onChange }) {
    return (
        <>
            <div className="form-group">
                <label className="form-label">Departamento</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.department || ''}
                    onChange={(e) => onChange('department', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Ex: Vendas, Suporte, Financeiro"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Agente específico (opcional)</label>
                <input
                    type="text"
                    className="form-input"
                    value={data.agent || ''}
                    onChange={(e) => onChange('agent', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Nome ou ID do agente"
                />
            </div>
            <div className="form-group">
                <label className="form-label">Mensagem de transferência</label>
                <textarea
                    className="form-textarea"
                    value={data.message || ''}
                    onChange={(e) => onChange('message', e.target.value)}
                    onKeyDown={handleInputEvent}
                    onMouseDown={handleInputEvent}
                    onClick={handleInputEvent}
                    placeholder="Aguarde, estamos transferindo você para um atendente..."
                />
            </div>
            <div className="form-group">
                <label className="form-label">Prioridade</label>
                <select
                    className="form-select"
                    value={data.priority || 'normal'}
                    onChange={(e) => onChange('priority', e.target.value)}
                >
                    <option value="low">Baixa</option>
                    <option value="normal">Normal</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </select>
            </div>
        </>
    );
}
