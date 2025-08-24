import { useEffect, useState } from 'react';
import axios from 'axios';
import { useTenantStore } from '@/stores/useTenantStore';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import dayjs from 'dayjs';
import { MultiSelect } from 'primereact/multiselect';
import { Calendar } from 'primereact/calendar';
import { InputText } from 'primereact/inputtext';
import { Button } from 'primereact/button';
import { router } from '@inertiajs/react';

// Función que encapsula la lógica para renderizar las columnas de valores antiguos y nuevos
function renderDiffValues(diffObj) {
    const values = diffObj || {};
    const isEmpty = Object.keys(values).length === 0;

    if (isEmpty) {
        return <span className="italic text-sm text-gray-400">Sin información</span>;
    }

    return (
        <ul className="pl-4 list-disc">
            { Object.entries(values).map(([ key, value ]) => (
                <li key={ key }>
                    <strong>{ key }:</strong> { String(value) }
                </li>
            )) }
        </ul>
    );
}

// Funcion para formatear los datos de la columna Entidad
function formatEntityAuditTable(table) {
    return table
        .replace('_audit', '')
        .replace(/_/g, ' ')
        .replace(/^./, str => str.toUpperCase());
};

// Estructura para Mapear la columna Type
const auditTypeMap = {
    1: 'Created',
    2: 'Updated',
    3: 'Deleted',
};

// Estructura para el filtro de columna Type
const auditTypeOptions = [
    { label: 'Created', value: 'created' },
    { label: 'Updated', value: 'updated' },
    { label: 'Deleted', value: 'deleted' },
];

export default function AuditTable() {
    const { tenant, tenantId } = useTenantStore();
    const [ records, setRecords ] = useState([]);

    // Filtro de Entidad
    const tablesFilter = [ 'users_audit', 'courses_audit', 'course_enrollments_audit' ];
    const auditTableOptions = tablesFilter.map((table) => ({
        label: formatEntityAuditTable(table),
        value: table,
    }));
    const [ selectedTables, setSelectedTables ] = useState([]);

    // Filtro de Tipo
    const [ selectedTypes, setSelectedTypes ] = useState([]);

    // Filtro por fechas
    const [ startDate, setStartDate ] = useState(null);
    const [ endDate, setEndDate ] = useState(null);

    // Busqueda de Object ID
    const [ objectIdInput, setObjectIdInput ] = useState('');
    const [ objectId, setObjectId ] = useState('');

    // Paginación
    const [ totalRecords, setTotalRecords ] = useState(0);
    const [ first, setFirst ] = useState(0);
    const [ rows, setRows ] = useState(4); // valor por defecto
    const [ loading, setLoading ] = useState(false);


    useEffect(() => {
        if (!tenantId || selectedTables.length == 0) {
            setRecords([]);
            setTotalRecords(0);
            return;
        };

        // paginación
        const page = Math.floor(first / rows) + 1;

        axios.get('/api/audit-records', {
            headers: {
                'X-Tenant-Id': tenantId,
            },
            params: {
                tables: selectedTables,
                types: selectedTypes,
                start_date: startDate,
                end_date: endDate,
                object_id: objectId,
                page: page,
                per_page: rows,
            },
        }).then((response) => {
            setRecords(response.data.data);
            setTotalRecords(response.data.total);
        }).catch((error) => {
            console.error('Error al obtener registros de auditoría:', error);
        }).finally(() => {
            setLoading(false);
        });
    }, [ tenantId, selectedTables, selectedTypes, startDate, endDate, objectId, first, rows ]);

    return (
        <div className='p-12'>
            <Button
                icon="pi pi-arrow-left"
                label="Volver al Clientes"
                className="mb-4"
                link
                onClick={() => router.visit('/tenants')}
            />
            <h2 className="text-xl font-bold mb-2">Tabla de Auditoría para cliente: { tenant.name }</h2>
            <h3 className="text-l font-bold mb-4">ID Cliente: { tenant.id }</h3>
            <div className="flex flex-col gap-4 mb-4">
                <label className="font-semibold">Entidad</label>
                <MultiSelect
                    value={ selectedTables }
                    options={ auditTableOptions }
                    onChange={ (e) => setSelectedTables(e.value) }
                    optionLabel="label"
                    placeholder="Selecciona una o más entidades"
                    display="chip"
                    className="w-full md:w-30rem"
                />
            </div>
            <div className="flex flex-col gap-4 mb-4">
                <label className="font-semibold">Tipo de acción</label>
                <MultiSelect
                    value={ selectedTypes }
                    options={ auditTypeOptions }
                    onChange={ (e) => setSelectedTypes(e.value) }
                    optionLabel="label"
                    placeholder="Selecciona uno o más tipos"
                    display="chip"
                    className="w-full md:w-30rem"
                />
            </div>
            <div className="flex flex-col gap-4 mb-4">
                <label className="font-semibold">Rango de fechas</label>
                <div className="flex gap-4">
                    <Calendar
                        value={ startDate }
                        onChange={ (e) => setStartDate(e.value) }
                        placeholder="Desde"
                        dateFormat="dd/mm/yy"
                        showIcon
                    />
                    <Calendar
                        value={ endDate }
                        onChange={ (e) => setEndDate(e.value) }
                        placeholder="Hasta"
                        dateFormat="dd/mm/yy"
                        showIcon
                    />
                </div>
            </div>
            <div className="flex flex-col gap-2 mb-6">
                <label className="font-semibold">Buscar por ID de Objeto</label>
                <div className="flex gap-3 w-full md:w-30rem">
                    <InputText
                        value={ objectIdInput }
                        onChange={ (e) => setObjectIdInput(e.target.value) }
                        placeholder="Ej: 9c9cf83d-5228-4a6c-8b37-85a550041be6"
                        className="flex-1"
                    />
                    <Button
                        icon="pi pi-search"
                        label="Buscar"
                        onClick={ () => setObjectId(objectIdInput.trim()) }
                    />
                    <Button
                        icon="pi pi-times"
                        label="Limpiar"
                        severity="secondary"
                        outlined
                        onClick={ () => {
                            setObjectId('');
                            setObjectIdInput('');
                        } }
                    />
                </div>
            </div>

            <div className="card">
                <DataTable
                    value={ records }
                    lazy
                    paginator
                    first={ first }
                    rows={ rows }
                    totalRecords={ totalRecords }
                    loading={ loading }
                    onPage={ (e) => {
                        setFirst(e.first);
                        setRows(e.rows);
                    } }
                    tableStyle={ { minWidth: '60rem' } }
                    showGridlines
                    emptyMessage="Sin información de auditorías"
                >
                    <Column field="id" header="ID" />
                    <Column
                        field="created_at"
                        header="Fecha"
                        body={ (rowData) => dayjs(rowData.created_at).format('DD/MM/YYYY HH:mm') }
                    />
                    <Column
                        field="type"
                        header="Acción"
                        body={ (rowData) => auditTypeMap[ rowData.type ] || 'Desconocido' }
                    />
                    <Column
                        field="audit_table"
                        header="Entidad"
                        body={ (rowData) => formatEntityAuditTable(rowData.audit_table) }
                    />
                    <Column field="object_id" header="ID Objeto" />
                    <Column field="blame_user" header="Usuario" />
                    <Column
                        header="Valores Antiguos"
                        body={ (rowData) => renderDiffValues(rowData.diffs?.old_values) }
                    />
                    <Column
                        header="Valores Nuevos"
                        body={ (rowData) => renderDiffValues(rowData.diffs?.new_values) }
                    />
                </DataTable>
            </div>
        </div>
    );
}
