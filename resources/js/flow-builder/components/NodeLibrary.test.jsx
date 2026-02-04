import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import NodeLibrary from './NodeLibrary';

describe('NodeLibrary', () => {
    it('renders all node categories', () => {
        render(<NodeLibrary />);

        expect(screen.getByText('Controle')).toBeInTheDocument();
        expect(screen.getByText('Comunicação')).toBeInTheDocument();
        expect(screen.getByText('Lógica')).toBeInTheDocument();
        expect(screen.getByText('Dados')).toBeInTheDocument();
        expect(screen.getByText('Ações')).toBeInTheDocument();
    });

    it('renders control nodes', () => {
        render(<NodeLibrary />);

        expect(screen.getByText('Início')).toBeInTheDocument();
        expect(screen.getByText('Fim')).toBeInTheDocument();
    });

    it('renders communication nodes', () => {
        render(<NodeLibrary />);

        expect(screen.getByText('Mensagem')).toBeInTheDocument();
        expect(screen.getByText('Pergunta')).toBeInTheDocument();
        expect(screen.getByText('Mídia')).toBeInTheDocument();
        expect(screen.getByText('Localização')).toBeInTheDocument();
        expect(screen.getByText('Contato')).toBeInTheDocument();
        expect(screen.getByText('Reação')).toBeInTheDocument();
    });

    it('renders logic nodes', () => {
        render(<NodeLibrary />);

        expect(screen.getByText('Condição')).toBeInTheDocument();
        expect(screen.getByText('Switch')).toBeInTheDocument();
        expect(screen.getByText('Randomizar')).toBeInTheDocument();
        expect(screen.getByText('Horário')).toBeInTheDocument();
        expect(screen.getByText('Aguardar')).toBeInTheDocument();
    });

    it('renders data nodes', () => {
        render(<NodeLibrary />);

        expect(screen.getByText('Variável')).toBeInTheDocument();
        expect(screen.getByText('Webhook')).toBeInTheDocument();
    });

    it('renders action nodes', () => {
        render(<NodeLibrary />);

        expect(screen.getByText('Ação')).toBeInTheDocument();
        expect(screen.getByText('Integração')).toBeInTheDocument();
        expect(screen.getByText('Transferir')).toBeInTheDocument();
    });

    it('node items are draggable', () => {
        render(<NodeLibrary />);

        const messageNode = screen.getByText('Mensagem').closest('.node-item');
        expect(messageNode).toHaveAttribute('draggable', 'true');
    });

    it('sets correct data on drag start', () => {
        render(<NodeLibrary />);

        const messageNode = screen.getByText('Mensagem').closest('.node-item');
        
        const mockDataTransfer = {
            setData: vi.fn(),
            effectAllowed: '',
        };

        fireEvent.dragStart(messageNode, { dataTransfer: mockDataTransfer });

        expect(mockDataTransfer.setData).toHaveBeenCalledWith('application/reactflow', 'message');
        expect(mockDataTransfer.effectAllowed).toBe('move');
    });
});
