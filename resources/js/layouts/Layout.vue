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
                        <Button v-if="userStore.notificationsCountFetched" plain text icon="pi pi-bell" :badge="userStore.notificationsCount > 0 ? userStore.notificationsCount.toString() : null" badgeClass="p-badge-success" :label="notificationLabelDisplayed ? 'Notifications' : null"/>
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
                        <Button v-if="userStore.notificationsCountFetched" plain text icon="pi pi-bell" label="Notifications" :badge="userStore.notificationsCount > 0 ? userStore.notificationsCount.toString() : null" badgeClass="p-badge-success"/>
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
                <img :src="message.icon" class="notification-icon" alt="Notification's icon"/>
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
            <component :darkMode="darkMode" :is="Component" :responsiveMenuDisplayed="responsiveMenuDisplayed"/>
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
