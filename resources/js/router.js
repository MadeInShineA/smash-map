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
                component:Home
            },
            {
                path: 'map',
                component: Map
            },
            {
                path: 'calendar',
                component: Calendar
            },
            {
                path: 'events',
                component: Events
            },
            {
                path: 'create-event',
                component: CreateEvent
            },
            {
                path: 'login',
                component: Login
            },
            {
                path: 'settings',
                component: Settings

            },
            {
                path: 'notifications',
                component: Notifications
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
