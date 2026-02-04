import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { ReactFlowProvider } from '@xyflow/react';
import StartNode from './StartNode';
import EndNode from './EndNode';
import MessageNode from './MessageNode';
import QuestionNode from './QuestionNode';
import ConditionNode from './ConditionNode';
import SwitchNode from './SwitchNode';
import DelayNode from './DelayNode';
import MediaNode from './MediaNode';
import LocationNode from './LocationNode';
import ContactNode from './ContactNode';
import ReactionNode from './ReactionNode';
import VariableNode from './VariableNode';
import WebhookNode from './WebhookNode';
import TransferNode from './TransferNode';

const renderWithProvider = (component) => {
    return render(
        <ReactFlowProvider>
            {component}
        </ReactFlowProvider>
    );
};

describe('StartNode', () => {
    it('renders with default label', () => {
        renderWithProvider(
            <StartNode data={{ label: 'Início', trigger: 'any' }} selected={false} />
        );
        expect(screen.getByText('Início')).toBeInTheDocument();
    });

    it('shows keyword trigger info', () => {
        renderWithProvider(
            <StartNode data={{ label: 'Início', trigger: 'keyword', keyword: 'oi, olá' }} selected={false} />
        );
        expect(screen.getByText(/oi, olá/)).toBeInTheDocument();
    });
});

describe('EndNode', () => {
    it('renders with default label', () => {
        renderWithProvider(
            <EndNode data={{ label: 'Fim', markAsCompleted: true }} selected={false} />
        );
        expect(screen.getByText('Fim')).toBeInTheDocument();
    });
});

describe('MessageNode', () => {
    it('renders with message preview', () => {
        renderWithProvider(
            <MessageNode data={{ label: 'Mensagem', text: 'Olá, como posso ajudar?' }} selected={false} />
        );
        expect(screen.getByText('Mensagem')).toBeInTheDocument();
        expect(screen.getByText(/Olá, como posso ajudar/)).toBeInTheDocument();
    });

    it('shows placeholder when no text', () => {
        renderWithProvider(
            <MessageNode data={{ label: 'Mensagem', text: '' }} selected={false} />
        );
        expect(screen.getByText('(mensagem vazia)')).toBeInTheDocument();
    });
});

describe('QuestionNode', () => {
    it('renders with question preview', () => {
        renderWithProvider(
            <QuestionNode data={{ label: 'Pergunta', question: 'Qual seu nome?' }} selected={false} />
        );
        expect(screen.getByText('Pergunta')).toBeInTheDocument();
        expect(screen.getByText(/Qual seu nome/)).toBeInTheDocument();
    });
});

describe('ConditionNode', () => {
    it('renders with condition info', () => {
        renderWithProvider(
            <ConditionNode 
                data={{ 
                    label: 'Condição', 
                    variable: 'resposta', 
                    operator: 'equals', 
                    value: 'sim' 
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Condição')).toBeInTheDocument();
    });
});

describe('SwitchNode', () => {
    it('renders with cases count', () => {
        renderWithProvider(
            <SwitchNode 
                data={{ 
                    label: 'Switch', 
                    variable: 'opcao',
                    cases: [
                        { value: 'vendas' },
                        { value: 'suporte' },
                    ]
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Switch')).toBeInTheDocument();
        expect(screen.getByText(/2 caso\(s\)/)).toBeInTheDocument();
    });
});

describe('DelayNode', () => {
    it('renders with delay info', () => {
        renderWithProvider(
            <DelayNode data={{ label: 'Aguardar', duration: 30, unit: 'seconds' }} selected={false} />
        );
        expect(screen.getByText('Aguardar')).toBeInTheDocument();
    });
});

describe('MediaNode', () => {
    it('renders with media type', () => {
        renderWithProvider(
            <MediaNode data={{ label: 'Mídia', mediaType: 'image', url: 'https://example.com/image.jpg' }} selected={false} />
        );
        expect(screen.getByText('Mídia')).toBeInTheDocument();
        expect(screen.getByText(/Imagem/)).toBeInTheDocument();
    });
});

describe('LocationNode', () => {
    it('renders with location info', () => {
        renderWithProvider(
            <LocationNode 
                data={{ 
                    label: 'Localização', 
                    name: 'Escritório',
                    latitude: '-23.55',
                    longitude: '-46.63'
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Localização')).toBeInTheDocument();
    });
});

describe('ContactNode', () => {
    it('renders with contact info', () => {
        renderWithProvider(
            <ContactNode 
                data={{ 
                    label: 'Contato', 
                    contactName: 'João Silva',
                    phone: '+55 11 99999-9999'
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Contato')).toBeInTheDocument();
        expect(screen.getByText(/João Silva/)).toBeInTheDocument();
    });
});

describe('ReactionNode', () => {
    it('renders with emoji', () => {
        renderWithProvider(
            <ReactionNode data={{ label: 'Reação', emoji: '👍' }} selected={false} />
        );
        expect(screen.getByText('Reação')).toBeInTheDocument();
        expect(screen.getByText('👍')).toBeInTheDocument();
    });
});

describe('VariableNode', () => {
    it('renders with variable info', () => {
        renderWithProvider(
            <VariableNode 
                data={{ 
                    label: 'Variável', 
                    variableName: 'contador',
                    operation: 'increment'
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Variável')).toBeInTheDocument();
        expect(screen.getByText(/contador/)).toBeInTheDocument();
    });
});

describe('WebhookNode', () => {
    it('renders with webhook info', () => {
        renderWithProvider(
            <WebhookNode 
                data={{ 
                    label: 'Webhook', 
                    method: 'POST',
                    url: 'https://api.example.com/webhook'
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Webhook')).toBeInTheDocument();
        expect(screen.getByText('POST')).toBeInTheDocument();
    });
});

describe('TransferNode', () => {
    it('renders with transfer info', () => {
        renderWithProvider(
            <TransferNode 
                data={{ 
                    label: 'Transferir', 
                    department: 'Vendas'
                }} 
                selected={false} 
            />
        );
        expect(screen.getByText('Transferir')).toBeInTheDocument();
        expect(screen.getByText(/Vendas/)).toBeInTheDocument();
    });
});
