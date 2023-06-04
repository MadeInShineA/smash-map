import'./bootstrap';

import "primeicons/primeicons.css";

import {createApp} from 'vue'

import App from './App.vue'

const app = createApp(App);

import {router} from'./router'
app.use(router)

import PrimeVue from 'primevue/config';
app.use(PrimeVue)

import TabMenu from 'primevue/tabmenu';
app.component('TabMenu', TabMenu)

import Menubar from 'primevue/menubar';
app.component('Menubar', Menubar)

import Button from "primevue/button";
app.component('Button', Button)

import Card from 'primevue/card';
app.component('Card', Card)

import Chip from 'primevue/chip';
app.component('Chip', Chip)

import ScrollTop from 'primevue/scrolltop';
app.component('ScrollTop', ScrollTop)

import {Qalendar} from "qalendar";
app.use(Qalendar)

app.mount("#app")
