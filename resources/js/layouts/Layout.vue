<script setup>
import {computed, onMounted, ref, watch} from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import {useWindowSize} from '@vueuse/core'
import LoginDialog from "@/components/LoginDialog.vue";
import Avatar from "primevue/avatar";
import Menu from "primevue/menu";
import {useDark} from '@vueuse/core'
import Swal from "sweetalert2";
import RegisterDialog from "@/components/RegisterDialog.vue";
import {usePrimeVue} from 'primevue/config';
import { useRouter } from 'vue-router';
import { useUserStore } from "../stores/UserStore.js";


const { width, height } = useWindowSize()
const responsiveMenuDisplayed = ref(false)

const userStore = useUserStore()

watch(width, function (width){
    responsiveMenuDisplayed.value = width <= 960
}, {immediate: true})


const menuItems = ref([
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
]);



const PrimeVue = usePrimeVue();

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

if (userStore.user) {
    userStore.subscribeToNotifications()
}

const showLoginModal = ref(false)

const switchShowLoginModal = function (){
    showLoginModal.value = !showLoginModal.value
}

const showRegisterModal = ref(false)

const switchShowRegisterModal = function (){
    showRegisterModal.value = !showRegisterModal.value
}

const router = useRouter()



// function setUser(){
//     user.value = JSON.parse(window.localStorage.getItem('userData'));
//     Echo.private(`notifications.` + user.value.id).listen('NotificationEvent', (e) => {
//         notificationsCount.value = (parseInt(notificationsCount.value) + 1).toString()
//     });
//     console.log(router.currentRoute.value)
//
//     addressesFilterStore.fetchAddressesWithFilters()
//     eventsFilterStore.fetchEventsWithFilters()
// }

const profileMenu = ref();

const toggleProfileMenu = function(event) {
    profileMenu.value.toggle(event);
}

const profileItems = ref([{
    label: 'Account',
    items: [
        {   label: 'Profile',
            icon: 'pi pi-fw pi-user'
        },
        {   label: 'Settings',
            icon: 'pi pi-fw pi-cog',
            to: 'settings'
        },
        {label: 'Log Out', icon: 'pi pi-sign-out', command: async function () {
                await userStore.logout().then(async function () {
                    const alertBackground = darkMode.value ? '#1C1B22' : '#FFFFFF'
                    const alertColor = darkMode.value ? '#FFFFFF' : '#1C1B22'
                    await Swal.fire({
                        title: 'Logged out!',
                        text: 'Your are successfully logged out!',
                        icon: 'success',
                        background: alertBackground,
                        color: alertColor,
                        timer: 2000,
                        showConfirmButton: false
                    })
                })
            }},
    ]
}])


</script>

<template>
    <header ref="menuBar" id="header">
        <Menubar v-if="responsiveMenuDisplayed" id="responsive-menu" :model="menuItems">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
                <template v-if="!userStore.user">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="userStore.user.profile_picture" shape="circle"  />
                    </Button>
                    <Menu :model="profileItems" :popup="true" ref="profileMenu"></Menu>
                    <router-link to="/notifications">
                        <Button plain text icon="pi pi-bell" label="Notifications" :badge="userStore.notificationsCount" badgeClass="p-badge-success"/>
                    </router-link>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
        </Menubar>
        <Menubar v-else :model="menuItems">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
            </template>
            <template #end>
                <template v-if="!userStore.user">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <router-link to="/notifications">
                        <Button plain text icon="pi pi-bell" label="Notifications" :badge="userStore.notificationsCount" badgeClass="p-badge-success"/>
                    </router-link>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="userStore.user.profile_picture" shape="circle"  />
                    </Button>
                    <Menu :model="profileItems" :popup="true" ref="profileMenu"></Menu>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" plain text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" plain text rounded aria-label="Sun"/>
            </template>
        </Menubar>
    </header>
    <main>
        <LoginDialog :darkMode="darkMode" :showLoginModal="showLoginModal" @switchShowLoginModal="switchShowLoginModal"/>
        <RegisterDialog :darkMode="darkMode" :showRegisterModal="showRegisterModal" @switchShowRegisterModal="switchShowRegisterModal"/>
        <router-view  v-slot="{ Component }">
            <keep-alive :include="['map', 'calendar']">
                <component :is="Component" :responsiveMenuDisplayed="responsiveMenuDisplayed"/>
            </keep-alive>
        </router-view>
    </main>

</template>

<style scoped>

#header .router-link-active-exact :deep(.p-menuitem-text){
    color: #3B82F6;
    font-weight:bold;
}

/*
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
 */

main{
    height: calc(100vh - v-bind(menuBarHeight));
    overflow-y: auto;
}

@keyframes scalein {
    0% {
        opacity: 0;
        transform: scaleY(0.8);
        transition: transform .12s cubic-bezier(0, 0, 0.2, 1), opacity .12s cubic-bezier(0, 0, 0.2, 1);
    }
    100% {
        opacity: 1;
        transform: scaleY(1);
    }
}

:deep(.p-menubar-start){
    display: flex;
    align-items: center; /* Center items vertically */
}

:deep(.p-menubar-end){
    display: flex;
    align-items: center; /* Center items vertically */
}

#responsive-menu{
    justify-content: space-between;
}

.p-menubar-mobile-active :deep(.p-menubar-root-list){
    animation-name: scalein;
    animation-duration: 0.5s;
}

#profile-avatar-button{
    padding: 0;
    min-width: min-content;
    margin-right:10px;
}

#responsive-menu #profile-avatar-button{
    padding: 0;
    min-width: min-content;
    margin-left:10px;
}

#sun-icon{
    color: orange;
}

#moon-icon{
    color: white;
}


</style>
