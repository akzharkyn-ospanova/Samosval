import 'mdb-vue-ui-kit'; 

import { createApp } from 'vue'; 

import Navigation from './components/Navigation.vue'; 

import { routes } from './routes.js'; 
import { createRouter, createWebHistory } from 'vue-router'; 

const router = createRouter({ 
    history: createWebHistory(),
    routes: routes 
})

const app = createApp(Navigation) 
    .use(router) 
    .mount('#app'); 
