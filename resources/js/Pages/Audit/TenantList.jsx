import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { router } from '@inertiajs/react';
import { useTenantStore } from '@/stores/useTenantStore';
import { Dialog } from 'primereact/dialog';
import { Button } from 'primereact/button';

function goToAudits(tenant) {
    const { setTenant } = useTenantStore.getState();
    setTenant(tenant);

    router.get(`/tenants/${tenant.id}/audit-records`, {}, {
        headers: {
            'X-Tenant-Id': tenant.id,
        },
    });
}

export default function TenantList() {
    const [ tenants, setTenants ] = useState([]);
    const [ loading, setLoading ] = useState(true);
    const [ error, setError ] = useState(null);
    const [ showErrorDialog, setShowErrorDialog ] = useState(false);

    useEffect(() => {
        setLoading(true)
        axios.get('/api/tenants')
            .then(response => {
                setTenants(response.data.data);
            })
            .catch(error => {
                setError('Error al cargar los tenants');
                setShowErrorDialog(true);
                console.error(error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) return <p>Cargando Clientes...</p>;

    return (
        <div>
            <div className='flex flex-col items-center p-12'>
                <h1 className='text-2xl'>Lista de Clientes</h1>
                <div className='py-4'>
                    <ul className="list-disc">
                        { tenants.map((tenant) => (
                            <li key={ tenant.id }>
                                <button onClick={ () => goToAudits(tenant) } className="text-lg text-blue-600 hover:underline">
                                    { tenant.name }
                                </button>
                            </li>
                        )) }
                    </ul>
                </div>
            </div>
            <Dialog
                header="Error"
                visible={ showErrorDialog }
                style={ { width: '30vw' } }
                modal
                onHide={ () => setShowErrorDialog(false) }
                footer={
                    <Button
                        label="Cerrar"
                        icon="pi pi-times"
                        onClick={ () => setShowErrorDialog(false) }
                        autoFocus
                    />
                }
            >
                <p className="m-0 text-red-500">{ error }</p>
            </Dialog>
        </div>
    );
}
