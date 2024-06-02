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
import Profile from "./views/Profile.vue";
import UserProfile from "@/views/UserProfile.vue";

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
            },
            {
                path: 'notifications',
                component: Notifications,
                name:'notifications'
            },
            {
                path: 'profile',
                component: Profile,
                name:'profile'
            },
            {
                path:'profile/:username',
                component: UserProfile,
                name:'user-profile',
                props: true
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
