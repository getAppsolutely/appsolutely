import type { AxiosStatic } from 'axios';
import type * as BootstrapNamespace from 'bootstrap';
import type { LoDashStatic } from 'lodash';

declare global {
    interface Window {
        axios: AxiosStatic;
        bootstrap: typeof BootstrapNamespace;
        _: LoDashStatic;
        assets?: Record<string, any>;
        MediaSliderCarousel?: any;
        showSelectedLocation?: (locationIndex: string | number) => void;
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
