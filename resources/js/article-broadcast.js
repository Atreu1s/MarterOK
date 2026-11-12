import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { createApp } from 'vue';
import ArticleBroadcast from './components/ArticleBroadcast.vue';

const mount = document.querySelector('#article-broadcast');
const key = import.meta.env.VITE_PUSHER_APP_KEY;

if (mount && key) {
    const echo = new Echo({
        broadcaster: 'pusher',
        client: new Pusher(key, {
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
        }),
    });

    createApp(ArticleBroadcast, { echo }).mount(mount);
}
