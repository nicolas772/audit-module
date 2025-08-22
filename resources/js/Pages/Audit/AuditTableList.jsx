import { useEffect, useState } from 'react';
import axios from 'axios';
import { useTenantStore } from '@/stores/useTenantStore';

export default function AuditTableList() {
    const { tenantId } = useTenantStore();
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
            <h2 className="text-xl font-bold mb-4">Tablas de Auditoría</h2>
            <ul className="list-disc ml-6">
                { Object.entries(tables).map(([ key, label ]) => (
                    <li key={ key }>
                        <a href='#' className="text-blue-600 hover:underline">
                            { label }
                        </a>
                    </li>
                )) }
            </ul>
        </div>
    );
}
