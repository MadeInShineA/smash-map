import { createRouter, createWebHistory} from 'vue-router'

import Home from "./views/Home.vue";
import Map from "./views/Map.vue";
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
            }
        ]

    },
]

export const router = createRouter({
    history: createWebHistory(''),
    routes,

})
