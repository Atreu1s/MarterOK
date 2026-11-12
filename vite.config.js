import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(async ({ mode }) => {
    const input = ['resources/css/app.css', 'resources/js/app.js'];
    const plugins = [];

    if (mode === 'broadcasting') {
        const { default: vue } = await import(import.meta.resolve('@vitejs/plugin-vue'));
        plugins.push(vue());
        input.push('resources/js/article-broadcast.js');
    }

    return { plugins: [laravel({ input, refresh: true }), ...plugins] };
});
