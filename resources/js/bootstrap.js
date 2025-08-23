import axios from 'axios';
window.axios = axios;
//import { useTenantStore } from '@/stores/useTenantStore';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/*axios.interceptors.request.use(config => {
  const { tenantId } = useTenantStore.getState();
  if (tenantId) {
    config.headers['X-Tenant-Id'] = tenantId;
  }
  return config;
});*/