<script setup>
import {computed, nextTick, onMounted, ref, watch} from "vue";
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
import Toast from "primevue/toast";


const { width, height } = useWindowSize()
const responsiveMenuDisplayed = ref(false)
const notificationLabelDisplayed = ref(false)

const userStore = useUserStore()

const toast = ref()

watch(width, function (width){
    responsiveMenuDisplayed.value = width <= 960
    notificationLabelDisplayed.value = width >= 400
}, {immediate: true})

const menuItems = ref([
    {
        label: 'Home',
        icon: 'pi pi-fw pi-home',
        route: '/'
    },
    {
        label: 'Map',
        icon: 'pi pi-fw pi-map',
        route: '/map'
    },
    {
        label: 'Calendar',
        icon: 'pi pi-fw pi-calendar',
        route: '/calendar'
    },
    {
        label: 'Events',
        icon: 'pi pi-fw pi-sitemap',
        items: [
            {
                label: 'Create',
                icon: 'pi pi-fw pi-plus',
                route: '/create-event'
            },
            {
                label: 'Browse',
                icon: 'pi pi-fw pi-search',
                route: '/events'
            },
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

const header = ref()

const headerHeight = ref()

if (userStore.user.data.id) {
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

const profileMenu = ref();

const toggleProfileMenu = function(event) {
    profileMenu.value.toggle(event);
}

const profileItems = ref([{
    label: 'Account',
    items: [
        {
            label: 'Profile',
            icon: 'pi pi-fw pi-user'
        },
        {
            label: 'Settings',
            icon: 'pi pi-fw pi-cog',
            route: 'settings'
        },
        {
            label: 'Log Out', icon: 'pi pi-sign-out', command: async function () {
                try {
                    await userStore.logout().then(async function (response) {
                        const alertBackground = darkMode.value ? '#1C1B22' : '#FFFFFF'
                        const alertColor = darkMode.value ? '#FFFFFF' : '#1C1B22'
                        await Swal.fire({
                            title: 'Logged out!',
                            text: response.data.message,
                            icon: 'success',
                            background: alertBackground,
                            color: alertColor,
                            timer: 2000,
                            showConfirmButton: false
                        })
                    })
                } catch (error) {
                    if (error.response && error.response.data.message && error.response.status === 500) {
                        const alertBackground = darkMode ? '#1C1B22' : '#FFFFFF'
                        const alertColor = darkMode ? '#FFFFFF' : '#1C1B22'
                        await Swal.fire({
                            title: 'Error',
                            text: error.response.data.message,
                            icon: 'error',
                            background: alertBackground,
                            color: alertColor,
                            timer: 2000,
                            showConfirmButton: false
                        })
                    } else {
                        console.log(error)
                    }
                }
            }
        }
    ]
}])

onMounted(()=>{
    console.log('Layout Mounted')
    userStore.toast = toast.value
    nextTick(() => {
        headerHeight.value = header.value.clientHeight + 'px';
    });
})

</script>
<template>
    <header ref="header" id="header">
        <Menubar v-if="responsiveMenuDisplayed" id="responsive-menu" :model="menuItems">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
                <template v-if="!userStore.user.data.id">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="userStore.user.data.profilePicture" shape="circle"  />
                    </Button>
                    <Menu :model="profileItems" :popup="true" ref="profileMenu">
                        <template #item="{ item, props }">
                            <router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
                                <a :href="href" v-bind="props.action" @click="navigate">
                                    <span :class="item.icon" />
                                    <span class="ml-2">{{ item.label }}</span>
                                </a>
                            </router-link>
                            <a v-else :href="item.url" :target="item.target" v-bind="props.action">
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                            </a>
                        </template>
                    </Menu>
                    <router-link to="/notifications">
                        <Button v-if="userStore.notificationsCountFetched" plain text icon="pi pi-bell" :badge="userStore.notificationsCount.toString()" badgeClass="p-badge-success" :label="notificationLabelDisplayed ? 'Notifications' : null"/>
                    </router-link>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
            <template #item="{ item, props, hasSubmenu }">
                <router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
                    <a :href="href" v-bind="props.action" @click="navigate">
                        <span :class="item.icon" />
                        <span class="ml-2">{{ item.label }}</span>
                    </a>
                </router-link>
                <a v-else :href="item.url" :target="item.target" v-bind="props.action">
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                    <span v-if="hasSubmenu" class="pi pi-fw pi-angle-down ml-2" />
                </a>
            </template>
        </Menubar>
        <Menubar v-else :model="menuItems">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
            </template>
            <template #item="{ item, props, hasSubmenu }">
                <router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
                    <a :href="href" v-bind="props.action" @click="navigate">
                        <span :class="item.icon" />
                        <span class="ml-2">{{ item.label }}</span>
                    </a>
                </router-link>
                <a v-else :href="item.url" :target="item.target" v-bind="props.action">
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                    <span v-if="hasSubmenu" class="pi pi-fw pi-angle-down ml-2" />
                </a>
            </template>
            <template #end>
                <template v-if="!userStore.user.data.id">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <router-link to="/notifications">
                        <Button v-if="userStore.notificationsCountFetched" plain text icon="pi pi-bell" label="Notifications" :badge="userStore.notificationsCount.toString()" badgeClass="p-badge-success"/>
                    </router-link>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="userStore.user.data.profilePicture" shape="circle"  />
                    </Button>
                    <Menu :model="profileItems" :popup="true" ref="profileMenu">
                        <template #item="{ item, props }">
                            <router-link v-if="item.route" v-slot="{ href, navigate }" :to="item.route" custom>
                                <a :href="href" v-bind="props.action" @click="navigate">
                                    <span :class="item.icon" />
                                    <span class="ml-2">{{ item.label }}</span>
                                </a>
                            </router-link>
                            <a v-else :href="item.url" :target="item.target" v-bind="props.action">
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                            </a>
                        </template>
                    </Menu>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" plain text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" plain text rounded aria-label="Sun"/>
            </template>
        </Menubar>
    </header>
    <main>
        <Toast ref="toast" :style="{'width': '70%', 'margin-top' : headerHeight, 'margin-left' : 0, 'margin-right': 0}" position="top-center" :breakpoints="{'725px': {width: '100%'}}">
            <template #message="{ message }">
                <img :src="message.icon" class="notification-icon"></img>
<!--                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="p-icon p-toast-message-icon" aria-hidden="true" data-pc-section="icon">-->
<!--                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.11101 12.8203C4.26215 13.5895 5.61553 14 7 14C8.85652 14 10.637 13.2625 11.9497 11.9497C13.2625 10.637 14 8.85652 14 7C14 5.61553 13.5895 4.26215 12.8203 3.11101C12.0511 1.95987 10.9579 1.06266 9.67879 0.532846C8.3997 0.00303296 6.99224 -0.13559 5.63437 0.134506C4.2765 0.404603 3.02922 1.07129 2.05026 2.05026C1.07129 3.02922 0.404603 4.2765 0.134506 5.63437C-0.13559 6.99224 0.00303296 8.3997 0.532846 9.67879C1.06266 10.9579 1.95987 12.0511 3.11101 12.8203ZM3.75918 2.14976C4.71846 1.50879 5.84628 1.16667 7 1.16667C8.5471 1.16667 10.0308 1.78125 11.1248 2.87521C12.2188 3.96918 12.8333 5.45291 12.8333 7C12.8333 8.15373 12.4912 9.28154 11.8502 10.2408C11.2093 11.2001 10.2982 11.9478 9.23232 12.3893C8.16642 12.8308 6.99353 12.9463 5.86198 12.7212C4.73042 12.4962 3.69102 11.9406 2.87521 11.1248C2.05941 10.309 1.50384 9.26958 1.27876 8.13803C1.05367 7.00647 1.16919 5.83358 1.61071 4.76768C2.05222 3.70178 2.79989 2.79074 3.75918 2.14976ZM7.00002 4.8611C6.84594 4.85908 6.69873 4.79698 6.58977 4.68801C6.48081 4.57905 6.4187 4.43185 6.41669 4.27776V3.88888C6.41669 3.73417 6.47815 3.58579 6.58754 3.4764C6.69694 3.367 6.84531 3.30554 7.00002 3.30554C7.15473 3.30554 7.3031 3.367 7.4125 3.4764C7.52189 3.58579 7.58335 3.73417 7.58335 3.88888V4.27776C7.58134 4.43185 7.51923 4.57905 7.41027 4.68801C7.30131 4.79698 7.1541 4.85908 7.00002 4.8611ZM7.00002 10.6945C6.84594 10.6925 6.69873 10.6304 6.58977 10.5214C6.48081 10.4124 6.4187 10.2652 6.41669 10.1111V6.22225C6.41669 6.06754 6.47815 5.91917 6.58754 5.80977C6.69694 5.70037 6.84531 5.63892 7.00002 5.63892C7.15473 5.63892 7.3031 5.70037 7.4125 5.80977C7.52189 5.91917 7.58335 6.06754 7.58335 6.22225V10.1111C7.58134 10.2652 7.51923 10.4124 7.41027 10.5214C7.30131 10.6304 7.1541 10.6925 7.00002 10.6945Z" fill="currentColor"></path>-->
<!--                </svg>-->
                <div class="p-toast-message-text" data-pc-section="text">
                    <span class="p-toast-summary" data-pc-section="summary">
                        {{message.summary}}
                    </span>
                    <div class="p-toast-detail" data-pc-section="detail" v-html="message.detail"></div>
                </div>
            </template>
        </Toast>
        <LoginDialog :darkMode="darkMode" :showLoginModal="showLoginModal" @switchShowLoginModal="switchShowLoginModal"/>
        <RegisterDialog :darkMode="darkMode" :showRegisterModal="showRegisterModal" @switchShowRegisterModal="switchShowRegisterModal"/>
        <router-view  v-slot="{ Component }">
            <keep-alive :include="['map', 'calendar']">
                <component :darkMode="darkMode" :is="Component" :responsiveMenuDisplayed="responsiveMenuDisplayed"/>
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
    height: calc(100vh - v-bind(headerHeight));
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

.p-toast-detail :deep(a){
    color: inherit;
}

.notification-icon{
    height:45px;
}


</style>
