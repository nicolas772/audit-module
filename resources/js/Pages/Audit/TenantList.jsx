import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { router } from '@inertiajs/react';

function goToAudits(tenantId) {
    router.visit(`/tenants/${tenantId}/audit-tables`);
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

    if (loading) return <p>Cargando tenants...</p>;
    if (error) return <p>{ error }</p>;

    return (
        <div className='p-12'>
            <h1>Lista de Tenants</h1>
            <ul>
                { tenants.map((tenant) => (
                    <li key={ tenant.id }>
                        <button onClick={ () => goToAudits(tenant.id) } className="text-blue-600 hover:underline">
                            { tenant.name }
                        </button>
                    </li>
                )) }
            </ul>
        </div>
    );
}
