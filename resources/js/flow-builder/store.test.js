import { describe, it, expect, beforeEach } from 'vitest';
import { useFlowStore } from './store';

describe('useFlowStore', () => {
    beforeEach(() => {
        // Reset store state before each test
        useFlowStore.setState({
            integrations: [],
            selectedNodeId: null,
            isDragging: false,
        });
    });

    describe('integrations', () => {
        it('should have empty integrations by default', () => {
            const state = useFlowStore.getState();
            expect(state.integrations).toEqual([]);
        });

        it('should set integrations', () => {
            const mockIntegrations = [
                { id: 1, provider: 'rdstation', status: 'connected' },
                { id: 2, provider: 'pipedrive', status: 'disconnected' },
            ];

            useFlowStore.getState().setIntegrations(mockIntegrations);

            const state = useFlowStore.getState();
            expect(state.integrations).toEqual(mockIntegrations);
            expect(state.integrations).toHaveLength(2);
        });
    });

    describe('selectedNodeId', () => {
        it('should have null selectedNodeId by default', () => {
            const state = useFlowStore.getState();
            expect(state.selectedNodeId).toBeNull();
        });

        it('should set selectedNodeId', () => {
            useFlowStore.getState().setSelectedNodeId('node-123');

            const state = useFlowStore.getState();
            expect(state.selectedNodeId).toBe('node-123');
        });

        it('should clear selectedNodeId', () => {
            useFlowStore.getState().setSelectedNodeId('node-123');
            useFlowStore.getState().setSelectedNodeId(null);

            const state = useFlowStore.getState();
            expect(state.selectedNodeId).toBeNull();
        });
    });

    describe('isDragging', () => {
        it('should have false isDragging by default', () => {
            const state = useFlowStore.getState();
            expect(state.isDragging).toBe(false);
        });

        it('should set isDragging to true', () => {
            useFlowStore.getState().setIsDragging(true);

            const state = useFlowStore.getState();
            expect(state.isDragging).toBe(true);
        });

        it('should set isDragging to false', () => {
            useFlowStore.getState().setIsDragging(true);
            useFlowStore.getState().setIsDragging(false);

            const state = useFlowStore.getState();
            expect(state.isDragging).toBe(false);
        });
    });
});
