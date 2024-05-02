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
import ResetPassword from "./views/ResetPassword.vue";
import {useUserStore} from "./stores/UserStore.js";

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
                name:'settings',
                beforeEnter: (to, from, next) => {

                    // TODO Make the user connected == session user check here or inside Settings.vue ?
                    const userStore = useUserStore();
                    if (userStore.user.data == null){
                        return next('/')
                    }
                    return next()
                }

            },
            {
                path: 'notifications',
                component: Notifications,
                name:'notifications'
            },
            {
                path: 'reset-password/:token',
                component: ResetPassword,
                name:'reset-password',
                props: true
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
