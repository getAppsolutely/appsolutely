import type { AxiosStatic } from 'axios';
import type * as BootstrapNamespace from 'bootstrap';

declare global {
  interface Window {
    axios: AxiosStatic;
    bootstrap: typeof BootstrapNamespace;
    _: typeof import('lodash');
    assets?: Record<string, any>;
  }

  interface ImportMetaEnv {
    VITE_PUSHER_APP_KEY: string;
    VITE_PUSHER_HOST: string;
    VITE_PUSHER_PORT: string;
    VITE_PUSHER_CLUSTER: string;
    VITE_PUSHER_SCHEME: string;
  }
}

export {};
