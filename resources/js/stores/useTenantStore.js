import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useTenantStore = create(
    persist(
        (set) => ({
            tenant: null,
            tenantId: null,
            setTenant: (tenant) => set({ tenant, tenantId: tenant.id }),
            clearTenant: () => set({ tenant: null, tenantId: null }),
        }),
        {
            name: 'tenant-storage', // Clave usada en localStorage
        }
    )
);
