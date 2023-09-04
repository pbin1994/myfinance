import './bootstrap';

import { createApp, ref   } from 'vue'

import axios from 'axios';
import VueAxios from 'vue-axios';
import Auth from '../components/Auth.js';
import VueMask from '@devindex/vue-mask';


import App from '../components/App.vue';
import router from '../components/routes.js';




const app = createApp(App)
app.config.globalProperties.auth = Auth;
app.use(router)
app.use(VueMask);
app.component('App')
app.mount('#app')
