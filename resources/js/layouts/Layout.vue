<script setup>
import {computed, onMounted, ref} from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import Dialog from 'primevue/dialog';

const items = ref([
    {
        label: 'Home',
        icon: 'pi pi-fw pi-home',
        to: '/'
    },
    {
        label: 'Map',
        icon: 'pi pi-fw pi-map',
        to: '/map'
    },
    {
        label: 'Calendar',
        icon: 'pi pi-fw pi-calendar',
        to: '/calendar'
    },
    {
        label: 'Events',
        icon: 'pi pi-fw pi-sitemap',
        items: [
            {
                label: 'Create',
                icon: 'pi pi-fw pi-plus',
                to: '/create-event'
            },
            {
                label: 'Browse',
                icon: 'pi pi-fw pi-search',
                to: '/events'
            }
        ]
    },
    {
        label: 'Login',
        icon: 'pi pi-user'
    },
    {
        label: 'Register',
        icon: 'pi pi-save'
    },


]);

import {usePrimeVue} from 'primevue/config';

const PrimeVue = usePrimeVue();

import { useDark } from '@vueuse/core'
const darkMode = useDark()
function switch_theme(changeMode) {
    if (changeMode){
        darkMode.value = !darkMode.value
    }
    darkMode.value
        ? PrimeVue.changeTheme(
            'mdc-light-indigo',
            'mdc-dark-indigo',
            'theme-link',
            () => {}
        )
        : PrimeVue.changeTheme(
            'mdc-dark-indigo',
            'mdc-light-indigo',
            'theme-link',
            () => {}
        )

}

switch_theme(false)

const menuBar = ref()

const menuBarHeight = computed(()=>menuBar.value.clientHeight + 'px')

const showLoginModal = ref(false)

const showRegisterModal = ref(false)

onMounted(() => {
    console.log('Default Layout Mounted')
})

</script>

<template>
    <header ref="menuBar" id="header">
        <Menubar :model="items">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
            </template>
            <template #end>
                <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
        </Menubar>
    </header>
    <main>
        <Dialog v-model:visible="showLoginModal" modal header="Login" :style="{ width: '50vw' }">
            <p>
                Login modal
            </p>
        </Dialog>
        <Dialog v-model:visible="showRegisterModal" modal header="Register" :style="{ width: '50vw' }">
            <p>
                Register modal
            </p>
        </Dialog>
        <router-view></router-view>
    </main>

</template>

<style>

#header .router-link-active-exact .p-menuitem-text{
    color: #3B82F6;
    font-weight:bold;
}

#scroll-top{
    margin-right: 10px;
    background-color: #E0E0E0
}

#scroll-top svg {
    color: black;
}

.dark #scroll-top{
    background-color: #393939
}

.dark #scroll-top svg {
    color: white;
}

main{
    height: calc(100vh - v-bind(menuBarHeight));
    overflow-y: auto;
}

#sun-icon{
    color: orange;
}

#moon-icon{
    color: white;
}
</style>
