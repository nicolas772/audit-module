import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { router } from '@inertiajs/react';
import { useTenantStore } from '@/stores/useTenantStore';

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

    useEffect(() => {
        axios.get('/api/tenants')
            .then(response => {
                setTenants(response.data.data);
            })
            .catch(error => {
                setError('Error al cargar los tenants');
                console.error(error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) return <p>Cargando Clientes...</p>;
    if (error) return <p>{ error }</p>;

    return (
        <div className='p-12'>
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
    );
}
