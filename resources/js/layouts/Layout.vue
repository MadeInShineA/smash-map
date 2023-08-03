<script setup>
import {computed, onMounted, ref, watch} from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import { useWindowSize } from '@vueuse/core'
import LoginDialog from "@/components/LoginDialog.vue";
import Avatar from "primevue/avatar";
import Menu from "primevue/menu";


const { width, height } = useWindowSize()
const responsiveMenuDisplayed = ref(false)

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

import {usePrimeVue} from 'primevue/config';

const PrimeVue = usePrimeVue();

import { useDark } from '@vueuse/core'
import axios from "axios";
import Swal from "sweetalert2";
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
const user = ref(null)
if (window.localStorage.getItem('userData') !== null) {
    user.value = JSON.parse(window.localStorage.getItem('userData'));
}

const registerUsername = ref('')

const registerEmail = ref('')

const registerPassword = ref('')

const showLoginModal = ref(false)

const switchShowLoginModal = function (){
    showLoginModal.value = !showLoginModal.value
}

const setUser = function(){
    user.value = JSON.parse(window.localStorage.getItem('userData'));
}

const showRegisterModal = ref(false)

const profileMenu = ref();

const toggleProfileMenu = function(event) {
    profileMenu.value.toggle(event);
}

const profileItems = ref([{
    label: 'Account',
    items: [
        {label: 'Profile', icon: 'pi pi-fw pi-user'},
        {label: 'Settings', icon: 'pi pi-fw pi-cog'},
        {label: 'Log Out', icon: 'pi pi-sign-out', command: async function () {
                console.log(axios.post('/logout'));
                localStorage.removeItem('userData');
                user.value = null;
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
            }},
    ]
}])


onMounted(() => {
    console.log('Default Layout Mounted')
})

</script>

<template>
    <header ref="menuBar" id="header">
        <Menubar v-if="responsiveMenuDisplayed" id="responsive-menu" :model="menuItems">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
                <template v-if="!user">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="user.profile_picture" :label="user.username[0]" shape="circle"  />
                    </Button>
                    <Menu :model="profileItems" :popup="true" ref="profileMenu"></Menu>
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
                <template v-if="!user">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <Button id="profile-avatar-button" plain text rounded @click="toggleProfileMenu">
                        <Avatar :image="user.profile_picture" :label="user.username[0]" shape="circle"  />
                    </Button>
                    <Menu :model="profileItems" :popup="true" ref="profileMenu"></Menu>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" plain text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" plain text rounded aria-label="Sun"/>
            </template>
        </Menubar>
    </header>
    <main>
        <LoginDialog :darkMode="darkMode" :showLoginModal="showLoginModal" @switchShowLoginModal="switchShowLoginModal" @setUser="setUser" ></LoginDialog>
<!--        <Dialog class="user-modal" v-model:visible="showRegisterModal" :draggable="false" modal header="Register">-->
<!--            <div class="modal-inputs">-->
<!--                <div class="p-float-label modal-input">-->
<!--                    <InputText id="register-username" v-model="registerUsername" required />-->
<!--                    <label for="register-username">Username</label>-->
<!--                </div>-->
<!--                <div class="p-float-label modal-input">-->
<!--                    <InputText id="register-email" v-model="registerEmail" />-->
<!--                    <label for="register-email">Email</label>-->
<!--                </div>-->
<!--                <div class="p-float-label modal-input">-->
<!--                    <Password id="register-password" v-model="registerPassword" toggleMask />-->
<!--                    <label for="register-password">Password</label>-->
<!--                </div>-->
<!--            </div>-->
<!--            <template #footer>-->
<!--                <Button label="Cancel" icon="pi pi-times" @click="showRegisterModal = false" text plain/>-->
<!--                <Button label="Register" icon="pi pi-check" @click="showRegisterModal = false" text plain/>-->
<!--            </template>-->
<!--        </Dialog>-->
        <router-view></router-view>
    </main>

</template>

<style scoped>

#header .router-link-active-exact >>> .p-menuitem-text{
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

>>> .p-menubar-start {
    display: flex;
    align-items: center; /* Center items vertically */
}

>>> .p-menubar-end {
    display: flex;
    align-items: center; /* Center items vertically */
}

#responsive-menu{
    justify-content: space-between;
}

.p-menubar-mobile-active >>> .p-menubar-root-list{
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
