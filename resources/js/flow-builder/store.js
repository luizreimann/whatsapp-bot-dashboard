import { create } from 'zustand';

export const useFlowStore = create((set) => ({
    integrations: [],
    setIntegrations: (integrations) => set({ integrations }),
    
    selectedNodeId: null,
    setSelectedNodeId: (id) => set({ selectedNodeId: id }),
    
    isDragging: false,
    setIsDragging: (isDragging) => set({ isDragging }),
}));
