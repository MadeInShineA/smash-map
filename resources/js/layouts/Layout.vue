<script setup>
import {computed, onMounted, ref} from "vue";


const items = ref([
    {
        label: 'Home',
        icon: 'pi pi-fw pi-home',
        to: '/home'
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

]);

import {usePrimeVue} from 'primevue/config';

const PrimeVue = usePrimeVue();

const darkMode = ref(false);
function switch_theme() {
    darkMode.value = !darkMode.value
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

const menuBar = ref()

const menuBarHeight = computed(()=>menuBar.value.clientHeight + 'px')

onMounted(() => {
    console.log('Default Layout Mounted')
})

</script>

<template>
    <header ref="menuBar">
        <Menubar :model="items">
            <template #start>
                <img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/>
            </template>
            <template #end>
                <Button v-if="!darkMode" @click="switch_theme" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="darkMode" @click="switch_theme" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
        </Menubar>
    </header>
    <main>
        <router-view></router-view>
    </main>
</template>

<style scoped>

.scroll-top{
    margin-right: 10px;
    border-radius:60px;
}


main{
    height: calc(100vh - v-bind(menuBarHeight));
    overflow-y: auto;
}

</style>
