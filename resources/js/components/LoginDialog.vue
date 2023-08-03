<script setup>

import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import {ref} from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    darkMode: Boolean,
    showLoginModal:Boolean
})

const emit = defineEmits(['switchShowLoginModal', 'setUser'])

const loginUser = ref({
    username: '',
    password: ''
})

const loginValidationErrors = ref({
    username: [],
    password: [],
    login:[]
})

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
            const response = await axios.post('/login', loginUser.value, header)
            localStorage.setItem('userData', JSON.stringify(response.data.data.user));
            // localStorage.setItem('accessToken', response.data.data.token)
            localStorage.setItem('tokenTime', new Date().toString());
            loginUser.value.username = ''
            loginUser.value.password = ''
            emit('setUser')
            emit('switchShowLoginModal')
            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            await Swal.fire({
                title: 'Logged in!',
                text: 'Your are successfully logged in!',
                icon: 'success',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
        } catch (error) {
            console.log(error)
            loginValidationErrors.value = error.response.data.errors
        }
    })
}
</script>

<template>
    <Dialog class="user-modal" :visible="showLoginModal" @update:visible="emit('switchShowLoginModal')" :draggable="false" modal header="Login">
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
            <Button label="Cancel" icon="pi pi-times" @click="emit('switchShowLoginModal')" text plain/>
            <Button label="Login" icon="pi pi-check" @click="login" text plain/>
        </template>
    </Dialog>
</template>

<style scoped>

.errors-enter-active,
.errors-leave-active {
    transition: opacity 0.5s ease;}
.errors-enter-from,
.errors-leave-to {
    opacity: 0;
}

.validation-error{
    margin-left: 20px;
    font-size: 12px;
    color: red;
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

</style>
