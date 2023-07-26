<script setup>
import {computed, onMounted, ref, watch} from "vue";
import Menubar from "primevue/menubar";
import Button from "primevue/button";
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Swal from 'sweetalert2'
import { useWindowSize } from '@vueuse/core'

const { width, height } = useWindowSize()
const responsiveMenuDisplayed = ref(false)

watch(width, function (width){
    responsiveMenuDisplayed.value = width <= 960
}, {immediate: true})


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

const loginUser = ref({
    username: '',
    password: ''
})

const loginValidationErrors = ref({
    username: [],
    password: [],
    login:[]
})

const user = ref({})
if (window.localStorage.getItem('userData') !== null) {
    user.value = JSON.parse(window.localStorage.getItem('userData'));
}

const login = async function () {
    loginValidationErrors.value.login = []

    const header = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    }


        axios.get('/sanctum/csrf-cookie').then(async () => {
            try {
                const response = await axios.post('/api/login', loginUser.value, header)
                user.value = response.data.data.user;
                localStorage.setItem('accessToken', response.data.data.token)
                localStorage.setItem('userData', JSON.stringify(user.value));
                localStorage.setItem('tokenTime', new Date().toString());
                loginUser.value.username = ''
                loginUser.value.password = ''
                showLoginModal.value = false
                const alertBackground = darkMode.value ? '#1C1B22' : '#FFFFFF'
                const alertColor = darkMode.value ? '#FFFFFF' : '#1C1B22'
                Swal.fire({
                    title: 'Logged in!',
                    text: 'Your are successfully logged in!',
                    icon: 'success',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
            } catch (error) {
                loginValidationErrors.value = error.response.data.errors
            }
        })

}

const registerUsername = ref('')

const registerEmail = ref('')

const registerPassword = ref('')

const showLoginModal = ref(false)

const showRegisterModal = ref(false)

onMounted(() => {
    console.log('Default Layout Mounted')
})

</script>

<template>
    <header ref="menuBar" id="header">
        <Menubar v-if="responsiveMenuDisplayed" id="responsive-menu" :model="items">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
                <template v-if="!user">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <Button text plain :label="user.username"/>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
        </Menubar>
        <Menubar v-else :model="items">
            <template #start>
                <router-link to="/"><img alt="logo" src="../../images/logo-no-text-no-bg.png" height="40" class="mr-2"/></router-link>
            </template>
            <template #end>
                <template v-if="!user">
                    <Button @click="showLoginModal = true" icon="pi pi-user" text plain label="Login"/>
                    <Button @click="showRegisterModal = true" icon="pi pi-save" text plain label="Register"/>
                </template>
                <template v-else>
                    <Button text plain :label="user.username"/>
                </template>
                <Button v-if="!darkMode" id="sun-icon" @click="switch_theme(true)" icon="pi pi-sun" severity="secondary" text rounded aria-label="Sun"/>
                <Button v-if="darkMode" id="moon-icon" @click="switch_theme(true)" icon="pi pi-moon" severity="secondary" text rounded aria-label="Sun"/>
            </template>
        </Menubar>
    </header>
    <main>
        <Dialog class="user-modal" v-model:visible="showLoginModal"  :draggable="false" modal header="Login">

            <div class="modal-inputs">
                <div class="p-float-label modal-input">
                    <InputText id="login-username" v-model="loginUser.username" @focus="loginValidationErrors.username = []"/>
                    <label for="login-username">Username</label>
                </div>
                <TransitionGroup name="errors">
                    <template v-for="loginUsernameError in loginValidationErrors.username" :key="loginUsernameError" class="validation-errors">
                            <div class="validation-error">{{loginUsernameError}}</div>
                    </template>
                </TransitionGroup>

                <div class="p-float-label modal-input">
                    <Password id="login-password" v-model="loginUser.password" :feedback="false" @focus="loginValidationErrors.password = []" toggleMask/>
                    <label for="login-password">Password</label>
                </div>
                <TransitionGroup name="errors">
                    <template v-for="loginPasswordError in loginValidationErrors.password" :key="loginPasswordError" class="validation-errors">
                        <div class="validation-error">{{loginPasswordError}}</div>
                    </template>
                </TransitionGroup>
                <TransitionGroup name="errors">
                    <template v-for="loginError in loginValidationErrors.login" :key="loginError" class="validation-errors">
                        <div class="validation-error">{{loginError}}</div>
                    </template>
                </TransitionGroup>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="showLoginModal = false" text plain/>
                <Button label="Login" icon="pi pi-check" @click="login" text plain/>
            </template>
        </Dialog>
        <Dialog class="user-modal" v-model:visible="showRegisterModal" :draggable="false" modal header="Register">
            <div class="modal-inputs">
                <div class="p-float-label modal-input">
                    <InputText id="register-username" v-model="registerUsername" required />
                    <label for="register-username">Username</label>
                </div>
                <div class="p-float-label modal-input">
                    <InputText id="register-email" v-model="registerEmail" />
                    <label for="register-email">Email</label>
                </div>
                <div class="p-float-label modal-input">
                    <Password id="register-password" v-model="registerPassword" toggleMask />
                    <label for="register-password">Password</label>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="showRegisterModal = false" text plain/>
                <Button label="Register" icon="pi pi-check" @click="showRegisterModal = false" text plain/>
            </template>
        </Dialog>
        <router-view></router-view>
    </main>

</template>

<style>

#header .router-link-active-exact .p-menuitem-text{
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

#responsive-menu{
    justify-content: space-between;
}

.p-menubar-mobile-active .p-menubar-root-list{
    animation-name: scalein;
    animation-duration: 0.5s;
}


.user-modal{
    width: max-content;
    display: flex;
    justify-content: center;
}

.modal-input{
    margin-left: 20px;
    margin-bottom: 10px;
    margin-top: 10px
}

.validation-error{
    margin-left: 20px;
    font-size: 12px;
    color: red;
}

.errors-enter-active,
.errors-leave-active {
    transition: opacity 0.5s ease;}
.errors-enter-from,
.errors-leave-to {
    opacity: 0;
}

#sun-icon{
    color: orange;
}

#moon-icon{
    color: white;
}

</style>
