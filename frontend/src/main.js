import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Set Axios Base URL (using relative path for Vite proxy)
axios.defaults.baseURL = '';

const token = localStorage.getItem('user_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);
app.use(router);
app.mount('#app');
