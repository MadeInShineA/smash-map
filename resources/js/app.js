import'./bootstrap';

import "primevue/resources/themes/lara-light-indigo/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";

import {createApp} from 'vue'

import App from './App.vue'

const app = createApp(App);

import {router} from'./router'
app.use(router)

import PrimeVue from 'primevue/config';
app.use(PrimeVue)

import TabMenu from 'primevue/tabmenu';
app.component('TabMenu',TabMenu)

import Menubar from 'primevue/menubar';
app.component('Menubar',Menubar)

app.mount("#app")
