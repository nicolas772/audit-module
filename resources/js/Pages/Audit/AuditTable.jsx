import { useEffect, useState } from 'react';
import axios from 'axios';
import { useTenantStore } from '@/stores/useTenantStore';

export default function AuditTable() {
    const { tenantId } = useTenantStore();
    // tables almacena las tablas de auditorias disponibles. Puede ser util para el filtro
    const [ tables, setTables ] = useState({});

    useEffect(() => {
        if (!tenantId) return;

        axios.get('/api/audit-tables', {
            headers: {
                'X-Tenant-Id': tenantId,
            },
        }).then(response => setTables(response.data));
    }, [ tenantId ]);

    return (
        <div className='p-12'>
            <h2 className="text-xl font-bold mb-4">Tabla de Auditoría para tenant ID: {tenantId}</h2>
        </div>
    );
}
