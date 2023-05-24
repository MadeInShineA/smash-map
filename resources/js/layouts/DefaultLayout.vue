
<script setup>
import {onMounted, ref} from "vue";


const items = ref([
    {
        label: 'Home',
        icon: 'pi pi-fw pi-home',
        to: '/home'
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
        label: 'Map',
        icon: 'pi pi-fw pi-map',
        to: '/map'
    },

]);

onMounted(()=>{
    console.log('Default Layout Mounted')
})

</script>

<template>

    <div class="card relative z-2">
        <Menubar :model="items">
            <template #start>
                <img alt="logo" src="./images/logo-no-text-no-bg.png" height="40" class="mr-2" />
            </template>
        </Menubar>
        <router-view></router-view>
    </div>

</template>

<style scoped>

</style>
