import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { ReactFlowProvider } from '@xyflow/react';
import BaseNode from './BaseNode';

const renderWithProvider = (component) => {
    return render(
        <ReactFlowProvider>
            {component}
        </ReactFlowProvider>
    );
};

describe('BaseNode', () => {
    it('renders with title and icon', () => {
        renderWithProvider(
            <BaseNode
                title="Test Node"
                icon="🔥"
                nodeType="test"
                selected={false}
            />
        );

        expect(screen.getByText('Test Node')).toBeInTheDocument();
        expect(screen.getByText('🔥')).toBeInTheDocument();
    });

    it('renders with preview content', () => {
        renderWithProvider(
            <BaseNode
                title="Test Node"
                icon="📝"
                nodeType="test"
                preview="Preview content here"
                selected={false}
            />
        );

        expect(screen.getByText('Preview content here')).toBeInTheDocument();
    });

    it('renders children content', () => {
        renderWithProvider(
            <BaseNode
                title="Test Node"
                icon="📝"
                nodeType="test"
                selected={false}
            >
                <div data-testid="child-content">Child Content</div>
            </BaseNode>
        );

        expect(screen.getByTestId('child-content')).toBeInTheDocument();
    });

    it('applies selected class when selected', () => {
        const { container } = renderWithProvider(
            <BaseNode
                title="Test Node"
                icon="📝"
                nodeType="test"
                selected={true}
            />
        );

        expect(container.querySelector('.custom-node.selected')).toBeInTheDocument();
    });

    it('applies correct node type class', () => {
        const { container } = renderWithProvider(
            <BaseNode
                title="Test Node"
                icon="📝"
                nodeType="message"
                selected={false}
            />
        );

        expect(container.querySelector('.node-message')).toBeInTheDocument();
    });
});
