import { createRouter, createWebHistory} from 'vue-router'

import Home from "./views/Home.vue";
import Map from "./views/Map.vue";
import Calendar from "./views/Calendar.vue";
import Events from "./views/Events.vue";
import CreateEvent from "./views/CreateEvent.vue";
import Login from "./views/Login.vue";
import DefaultLayout from "./layouts/Layout.vue";
import Notifications from "./views/Notifications.vue";
import Settings from "./views/Settings.vue";

const routes = [
    {
        path: '/',
        component: DefaultLayout,
        children: [
            {
                path:'',
                component:Home,
                name:'home'
            },
            {
                path: 'map',
                component: Map,
                name:'map'
            },
            {
                path: 'calendar',
                component: Calendar,
                name:'calendar'
            },
            {
                path: 'events',
                component: Events,
                name:'events'
            },
            {
                path: 'create-event',
                component: CreateEvent,
                name:'create-event'
            },
            {
                path: 'login',
                component: Login,
                name:'login'
            },
            {
                path: 'settings',
                component: Settings,
                name:'settings'

            },
            {
                path: 'notifications',
                component: Notifications,
                name:'notifications'
            },
            {
                path: ':catchAll(.*)',
                redirect: '/'
            }

        ]

    },
]


export const router = createRouter({
    history: createWebHistory(''),
    routes,

})
