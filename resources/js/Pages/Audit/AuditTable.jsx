import { useEffect, useState } from 'react';
import axios from 'axios';
import { useTenantStore } from '@/stores/useTenantStore';

import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';

export default function AuditTable() {
    const { tenantId } = useTenantStore();
    // tables almacena las tablas de auditorias disponibles. Puede ser util para el filtro
    const [ tables, setTables ] = useState({});
    const products = [
        {
            "code": "1",
            "name": "nombre 1",
            "category": "categoria 1",
            "quantity": 100
        },
        {
            "code": "2",
            "name": "nombre 2",
            "category": "categoria 2",
            "quantity": 200
        },
        {
            "code": "3",
            "name": "nombre 3",
            "category": "categoria 3",
            "quantity": 300
        },
    ]

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
            <h2 className="text-xl font-bold mb-4">Tabla de Auditoría para tenant ID: { tenantId }</h2>
            <div className="card">
                <DataTable value={ products } tableStyle={ { minWidth: '50rem' } }>
                    <Column field="code" header="Code"></Column>
                    <Column field="name" header="Name"></Column>
                    <Column field="category" header="Category"></Column>
                    <Column field="quantity" header="Quantity"></Column>
                </DataTable>
            </div>
        </div>
    );
}
