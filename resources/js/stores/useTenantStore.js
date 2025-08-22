import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useTenantStore = create(
    persist(
        (set) => ({
            tenantId: null,
            setTenantId: (id) => set({ tenantId: id }),
            clearTenantId: () => set({ tenantId: null }),
        }),
        {
            name: 'tenant-storage', // Clave usada en localStorage
        }
    )
);
