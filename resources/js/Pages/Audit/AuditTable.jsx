import { useEffect, useState } from 'react';
import axios from 'axios';
import { useTenantStore } from '@/stores/useTenantStore';

import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';

function renderDiffValues(diffObj) {
    const values = diffObj || {};
    const isEmpty = Object.keys(values).length === 0;

    if (isEmpty) {
        return <span className="italic text-sm text-gray-400">Sin información</span>;
    }

    return (
        <ul className="pl-4 list-disc">
            {Object.entries(values).map(([key, value]) => (
                <li key={key}>
                    <strong>{key}:</strong> {String(value)}
                </li>
            ))}
        </ul>
    );
}

export default function AuditTable() {
    const { tenantId } = useTenantStore();
    // tables almacena las tablas de auditorias disponibles. Puede ser util para el filtro
    const [ tables, setTables ] = useState({});
    const [ records, setRecords ] = useState([]);

    useEffect(() => {
        if (!tenantId) return;
        axios.get('/api/audit-tables', {
            headers: {
                'X-Tenant-Id': tenantId,
            }
        }).then((response) => {
            setTables(response.data.data);
        }).catch((error) => {
            console.error('Error al obtener registros de auditoría:', error);
        });
    }, [ tenantId ]);

    useEffect(() => {
        if (!tenantId) return;
        axios.get('/api/audit-records', {
            headers: {
                'X-Tenant-Id': tenantId,
            },
            params: {
                tables: ['users_audit', 'course_enrollments_audit'], // por ahora fijo
            },
        }).then((response) => {
            setRecords(response.data.data);
        }).catch((error) => {
            console.error('Error al obtener registros de auditoría:', error);
        });
    }, [tenantId]);

    return (
        <div className='p-12'>
            <h2 className="text-xl font-bold mb-4">Tabla de Auditoría para tenant ID: { tenantId }</h2>
            <div className="card">
                <DataTable value={ records } tableStyle={ { minWidth: '60rem' } }>
                    <Column field="id" header="ID" />
                    <Column field="created_at" header="Fecha" />
                    <Column field="type" header="Acción" />
                    <Column field="audit_table" header="entidad" />
                    <Column field="object_id" header="ID Objeto" />
                    <Column field="blame_user" header="Usuario" />
                    <Column
                        header="Valores Antiguos"
                        body={(rowData) => renderDiffValues(rowData.diffs?.old_values)}
                    />

                    <Column
                        header="Valores Nuevos"
                        body={(rowData) => renderDiffValues(rowData.diffs?.new_values)}
                    />
                </DataTable>
            </div>
        </div>
    );
}
