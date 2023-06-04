<script setup>
import {onMounted, ref} from "vue";


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

onMounted(() => {
    console.log('Default Layout Mounted')
})

import {usePrimeVue} from 'primevue/config';

const PrimeVue = usePrimeVue();
let default_theme = ref(true)

function switch_theme() {
    default_theme.value = !default_theme.value
    default_theme.value
        ? PrimeVue.changeTheme(
            'bootstrap4-dark-blue',
            'bootstrap4-light-blue',
            'theme-link',
            () => {}
        )
        : PrimeVue.changeTheme(
            'bootstrap4-light-blue',
            'bootstrap4-dark-blue',
            'theme-link',
            () => {}
        );
}

const visible = ref(false);

</script>

<template>

    <div class="card relative z-2">
        <Menubar :model="items" class="header">
            <template #start>
                <img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/>
            </template>
            <template #end>
<!--                <Button label="Show" icon="pi pi-external-link" @click="visible = true" />-->

<!--                <Dialog v-model:visible="visible" modal header="Login" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '100vw' }">-->
<!--                        <div class="flex flex-column gap-2">-->
<!--                            <label for="username">Username</label>-->
<!--                            <InputText id="username" aria-describedby="username-help" />-->
<!--                        </div>-->
<!--                </Dialog>-->
                <Button v-if="!default_theme" @click="switch_theme" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="default_theme" @click="switch_theme" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
        </Menubar>
        <div class="content">
            <router-view></router-view>
        </div>
        <ScrollTop class="scroll-top"></ScrollTop>
    </div>

</template>

<style scoped>

.header {
    position:sticky; /* fixing the position takes it out of html flow - knows
                   nothing about where to locate itself except by browser
                   coordinates */
    left:0;           /* top left corner should start at leftmost spot */
    top:0;            /* top left corner should start at topmost spot */
    width:100vw;      /* take up the full browser width */
    z-index:200;  /* high z index so other content scrolls underneath */
}

.scroll-top{
    border-radius:60px;
}

</style>
