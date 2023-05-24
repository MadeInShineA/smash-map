import { createRouter, createWebHistory} from 'vue-router'

import Home from "./views/Home.vue";
import Map from "./views/Map.vue";
import Calendar from "./views/Calendar.vue";
import Events from "./views/Events.vue";
import CreateEvent from "./views/CreateEvent.vue";
import DefaultLayout from "./layouts/DefaultLayout.vue";

const routes = [
    {
        path: '/',
        component:DefaultLayout,
        children:[
            {
                path:'home',
                component:Home
            },
            {
                path:'map',
                component: Map
            },
            {
                path:'calendar',
                component: Calendar
            },
            {
                path:'events',
                component: Events
            },
            {
                path:'create-event',
                component: CreateEvent
            }
        ]

    },
]

export const router = createRouter({
    history: createWebHistory(''),
    routes,

})
