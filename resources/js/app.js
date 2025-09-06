import './bootstrap';
import '../scss/app.scss';
import { createApp } from 'vue';
import router from './router';
import App from './App.vue';

createApp(App).use(router).mount('#app');