import { usePage } from '@inertiajs/react';

export default function TenantAuditList() {
    const { props } = usePage();
    const { tenantId } = props;

    return (
        <div>
            <h1>Auditorias de { tenantId }</h1>
        </div>
    );
}
