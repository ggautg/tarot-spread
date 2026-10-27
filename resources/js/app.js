import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { initTheme } from './composables/useTheme';

const appName = import.meta.env.VITE_APP_NAME || 'El Oráculo';

createInertiaApp({
    // La home ya se llama como la app: no la duplicamos ("El Oráculo · El Oráculo").
    title: (title) => (!title || title === appName ? appName : `${title} · ${appName}`),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        initTheme(props.initialPage.props);

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
