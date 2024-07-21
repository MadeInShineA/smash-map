<script setup>
import { computed, nextTick, onMounted, ref, watch } from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import { useWindowSize } from '@vueuse/core'
import LoginDialog from "@/components/LoginDialog.vue";
import Avatar from "primevue/avatar";
import Menu from "primevue/menu";
import { useDark } from '@vueuse/core'
import Swal from "sweetalert2";
import RegisterDialog from "@/components/RegisterDialog.vue";
import { usePrimeVue } from 'primevue/config';
import { useRouter } from 'vue-router';
import { useUserStore } from "../stores/UserStore.js";
import Toast from "primevue/toast";
import Sidebar from "primevue/sidebar";

const { width } = useWindowSize();
const responsiveMenuDisplayed = ref(false);
const notificationLabelDisplayed = ref(false);

const userStore = useUserStore();

const toast = ref();

watch(width, function (width) {
    responsiveMenuDisplayed.value = width <= 960;
    notificationLabelDisplayed.value = width >= 400;
}, { immediate: true });

const menuItems = ref([
    { label: 'Home', icon: 'pi pi-fw pi-home', route: '/' },
    { label: 'Map', icon: 'pi pi-fw pi-map', route: '/map' },
    { label: 'Events', icon: 'pi pi-fw pi-sitemap', route: '/events' },
    { label: 'About', icon: 'pi pi-fw pi-question-circle', route: '/about' },
    { label: 'Donate', icon: 'pi pi-fw pi-heart', url: 'https://ko-fi.com/madeinshinea', target: '_blank' },
]);

const PrimeVue = usePrimeVue();
const darkMode = useDark();

function switch_theme(changeMode) {
    if (changeMode) {
        darkMode.value = !darkMode.value;
    }
    darkMode.value
        ? PrimeVue.changeTheme('mdc-light-indigo', 'mdc-dark-indigo', 'theme-link', () => { })
        : PrimeVue.changeTheme('mdc-dark-indigo', 'mdc-light-indigo', 'theme-link', () => { });
}

switch_theme(false);

const header = ref();
const headerHeight = ref();

const showLoginModal = ref(false);
const switchShowLoginModal = function () {
    showLoginModal.value = !showLoginModal.value;
};

const showRegisterModal = ref(false);
const switchShowRegisterModal = function () {
    showRegisterModal.value = !showRegisterModal.value;
};

const router = useRouter();
const profileMenu = ref();

const toggleProfileMenu = function (event) {
    profileMenu.value.toggle(event);
};

const profileItems = ref([{
    label: 'Account',
    items: [
        { label: 'Profile', icon: 'pi pi-fw pi-user', route: '/profile' },
        { label: 'Settings', icon: 'pi pi-fw pi-cog', route: '/settings' },
        {
            label: 'Log Out', icon: 'pi pi-sign-out', command: async function () {
                try {
                    await userStore.logout().then(async function (response) {
                        const alertBackground = darkMode.value ? '#1C1B22' : '#FFFFFF';
                        const alertColor = darkMode.value ? '#FFFFFF' : '#1C1B22';
                        await Swal.fire({
                            title: 'Logged out!',
                            text: response.data.message,
                            icon: 'success',
                            background: alertBackground,
                            color: alertColor,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                } catch (error) {
                    if (error.response && error.response.data.message && error.response.status === 500) {
                        const alertBackground = darkMode ? '#1C1B22' : '#FFFFFF';
                        const alertColor = darkMode ? '#FFFFFF' : '#1C1B22';
                        await Swal.fire({
                            title: 'Error',
                            text: error.response.data.message,
                            icon: 'error',
                            background: alertBackground,
                            color: alertColor,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        console.log(error);
                    }
                }
            }
        }
    ]
}]);

const isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1;
const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
const displayInstallApp = ref(true);

const canBeInstalledAndroid = ref();
const isStandaloneAndroid = window.matchMedia('(display-mode: standalone)').matches;

const canBeInstalledIOS = 'standalone' in window.navigator;
const isInstalledIOS = window.navigator.standalone === true;

function setCanBeInstalled() {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        canBeInstalledAndroid.value = e;
    });
}

function promptInstallation() {
    canBeInstalledAndroid.value.prompt();
}

onMounted(() => {
    setCanBeInstalled();
    console.log('Layout Mounted');
    userStore.toast = toast.value;
    nextTick(() => {
        headerHeight.value = header.value.clientHeight + 'px';
        document.documentElement.style.setProperty('--header-height', headerHeight.value);
    });
});

</script>

<template>
    <header ref="header" id="header">
        <Menubar v-if="responsiveMenuDisplayed" id="responsive-menu" :model="menuItems">
            <template #start>
                <router-link to="/"><img alt="logo" src="/resources/images/logo-no-text-no-bg.png" height="40" class="mr-2" /></router-link>
                <template v-if="!userStore.user.data.id">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login" />
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register" />
                </template>
                <template v-else>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="userStore.user.data.profilePicture.url" shape="circle" />
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
                        <Button v-if="userStore.notificationsCountFetched" plain text icon="pi pi-bell" :badge="userStore.notificationsCount > 0 ? userStore.notificationsCount.toString() : null" badgeClass="p-badge-success" :label="notificationLabelDisplayed ? 'Notifications' : null" />
                    </router-link>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun" />
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Moon" />
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
                <router-link to="/"><img alt="logo" src="/resources/images/logo-no-text-no-bg.png" height="40" class="mr-2" /></router-link>
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
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login" />
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register" />
                </template>
                <template v-else>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="userStore.user.data.profilePicture.url" shape="circle" />
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
                        <Button v-if="userStore.notificationsCountFetched" plain text icon="pi pi-bell" :badge="userStore.notificationsCount > 0 ? userStore.notificationsCount.toString() : null" badgeClass="p-badge-success" :label="notificationLabelDisplayed ? 'Notifications' : null" />
                    </router-link>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun" />
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Moon" />
            </template>
        </Menubar>
    </header>
    <main>
        <router-view />
        <Sidebar>
            <template #header>
                <h2>Menu</h2>
            </template>
            <template #content>
                <Button label="Home" icon="pi pi-fw pi-home" @click="$router.push('/')"/>
                <Button label="Map" icon="pi pi-fw pi-map" @click="$router.push('/map')"/>
                <Button label="Events" icon="pi pi-fw pi-sitemap" @click="$router.push('/events')"/>
                <Button label="About" icon="pi pi-fw pi-question-circle" @click="$router.push('/about')"/>
                <Button label="Donate" icon="pi pi-fw pi-heart" @click="() => { window.open('https://ko-fi.com/madeinshinea', '_blank'); }"/>
            </template>
        </Sidebar>
    </main>
    <Toast ref="toast" />
    <LoginDialog v-model:show="showLoginModal" />
    <RegisterDialog v-model:show="showRegisterModal" />
</template>

<style scoped>
#header {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background-color: var(--surface-a);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

main {
    padding-top: var(--header-height);
    overflow-y: auto;
    box-sizing: border-box;
    height: calc(100vh - var(--header-height));
    /* Additional padding or margin adjustments as needed */
}

body, html {
    overflow-x: hidden;
    height: 100%;
}

#app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

router-view {
    display: flex;
    flex-direction: column;
    flex: 1;
}

#responsive-menu .p-menubar-start {
    flex-grow: 1;
}
</style>
