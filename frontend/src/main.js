import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Set Axios Base URL
axios.defaults.baseURL = 'http://127.0.0.1:8000';

const token = localStorage.getItem('user_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);
app.use(router);
app.mount('#app');
