import'./bootstrap';

import "primeicons/primeicons.css";
import "vue-awesome-paginate/dist/style.css";
import 'primevue/resources/primevue.css'
import {createApp} from 'vue'
import App from './App.vue'
import LoaderComponent from './components/LoaderComponent.vue';
import {router} from'./router'
import PrimeVue from 'primevue/config';
import {Qalendar} from "qalendar";

const app = createApp(App);

app.component('LoaderComponent', LoaderComponent)

app.use(router)

app.use(PrimeVue)

app.use(Qalendar)

app.mount("#app")
