import { createRouter, createWebHistory} from 'vue-router'

import Home from "./views/Home.vue";
import Map from "./views/Map.vue";
import Events from "./views/Events.vue";
import Login from "./views/Login.vue";
import DefaultLayout from "./layouts/Layout.vue";
import Notifications from "./views/Notifications.vue";
import Settings from "./views/Settings.vue";
import ResetPassword from "./views/ResetPassword.vue";
import Profile from "./views/Profile.vue";
import UserProfile from "./views/UserProfile.vue";
import About from "./views/About.vue";

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
                name: 'map',
                component: Map,
                props: route => ({
                    lat: route.query.lat,
                    lng: route.query.lng
                })
            },
            {
                path: 'events',
                component: Events,
                name:'events'
            },
            {
                path: 'about',
                component: About,
                name:'about'

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
