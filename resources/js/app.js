import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import VueToastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

createInertiaApp({
    title: (title) => (title ? `${title} — PharmaStream` : 'PharmaStream'),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const path = `./Pages/${name}.vue`;
        if (!pages[path]) {
            throw new Error(`Unknown page: ${name}`);
        }
        return pages[path]();
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(pinia);
        app.use(VueToastify, {
            autoClose: 2500,
            position: 'top-right',
            theme: 'colored',
        });
        app.mount(el);
    },
    progress: {
        color: '#059669',
    },
});
