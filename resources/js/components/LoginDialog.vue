<script setup>

import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import {ref} from "vue";
import Swal from "sweetalert2";
import {useUserStore} from "../stores/UserStore.js";
import FloatLabel from "primevue/floatlabel";


const userStore = useUserStore()

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true
    },
    showLoginModal:{
        type: Boolean,
        required: true
    }
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

    axios.get('/sanctum/csrf-cookie').then(async () => {
        try {
            await userStore.login(loginUser.value).then(async function (response) {
                loginUser.value.username = ''
                loginUser.value.password = ''
                emit('switchShowLoginModal')
                const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
                const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
                await Swal.fire({
                    title: 'Logged in!',
                    text: response.data.message,
                    icon: 'success',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
            })
        } catch (error) {
            if(error.response && error.response.data.errors === undefined && error.response.data.message && error.response.status === 500){
                emit('switchShowLoginModal')
                const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
                const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
                await Swal.fire({
                    title: 'Error',
                    text: error.response.data.message,
                    icon: 'error',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
            }else if(error.response && error.response.data.errors){
                loginValidationErrors.value = error.response.data.errors
            }else {
                console.log(error)
            }
        }
    })
}


const showForgetPasswordInputs = ref(false)
const forgotEmail = ref('')

const sendPasswordLink = async function () {
    forgotValidationErrors.value.email = []

    axios.get('/sanctum/csrf-cookie').then(async () => {
        try {
            await userStore.forgotPassword(forgotEmail.value).then(async function (response) {
                forgotEmail.value = ''
                emit('switchShowLoginModal')
                const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
                const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
                await Swal.fire({
                    title: 'Password reset link sent!',
                    text: response.data.message,
                    icon: 'success',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
            })
        } catch (error) {
            if(error.response.data.errors === undefined && error.response.data.message && error.status === 500){
                emit('switchShowLoginModal')
                const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
                const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
                await Swal.fire({
                    title: 'Error',
                    text: error.response.data.message,
                    icon: 'error',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
            }else if(error.response.data.errors){
                forgotValidationErrors.value = error.response.data.errors
            }else{
                console.log(error)
            }
        }
    })
}

const forgotValidationErrors = ref({
    email: []
})

</script>

<template>
    <Dialog id="login-modal" :visible="showLoginModal" @update:visible="emit('switchShowLoginModal')" :draggable="false" modal :header="showForgetPasswordInputs ? 'Forgot Password' : 'Login'" :style="{ minWidth: '300px' }" :breakpoints="{ '1200px': '50vw', '575px': '90vw' }">
        <div v-if="!showForgetPasswordInputs">
            <div class="modal-input-container">
                <FloatLabel>
                    <InputText id="login-username" class="modal-input" v-model="loginUser.username" autofocus @focus="loginValidationErrors.username = []"/>
                    <label for="login-username">Username</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="loginUsernameError in loginValidationErrors.username" :key="loginUsernameError" class="validation-errors">
                        <div class="validation-error">{{loginUsernameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <FloatLabel>
                    <Password id="login-password" class="modal-input" v-model="loginUser.password" :feedback="false" @focus="loginValidationErrors.password = []" toggleMask/>
                    <label for="login-password">Password</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="loginPasswordError in loginValidationErrors.password" :key="loginPasswordError" class="validation-errors">
                        <div class="validation-error">{{loginPasswordError}}</div>
                    </template>
                </TransitionGroup>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="loginError in loginValidationErrors.login" :key="loginError" class="validation-errors">
                        <div class="validation-error">{{loginError}}</div>
                    </template>
                </TransitionGroup>
            </div>
        </div>
        <div v-if="showForgetPasswordInputs">
            <div class="modal-input-container">
                <FloatLabel>
                    <InputText id="forgot-email" class="modal-input" v-model="forgotEmail" @focus="forgotValidationErrors.email = []"/>
                    <label for="forgot-email">Email</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="forgotEmailError in forgotValidationErrors.email" :key="forgotEmailError" class="validation-errors">
                        <div class="validation-error">{{forgotEmailError}}</div>
                    </template>
                </TransitionGroup>
            </div>
        </div>
        <template #footer>
            <div class="modal-footer">
                <Button :label="showForgetPasswordInputs ? '' : 'Forgot password'" severity="warning" :icon="showForgetPasswordInputs ? 'pi pi-arrow-left' : 'pi pi-exclamation-triangle'" @click="showForgetPasswordInputs = !showForgetPasswordInputs" text plain/>
                <div>
                    <Button label="Cancel" severity="danger" icon="pi pi-times" @click="emit('switchShowLoginModal')" text plain/>
                    <Button :label="showForgetPasswordInputs ? 'Send reset link' : 'Login'" severity="success" icon="pi pi-check" @click="() => showForgetPasswordInputs ? sendPasswordLink() : login()" text plain/>
                </div>
            </div>
        </template>
    </Dialog>
</template>

<style>

.p-dialog-content{
    padding: 0;
}

#login-modal_header{
    margin: auto;
}

</style>

<style scoped>



.errors-enter-active,
.errors-leave-active {
    transition: opacity 0.5s ease;}
.errors-enter-from,
.errors-leave-to {
    opacity: 0;
}

.validation-errors{
    min-height: 1em;
}

.validation-error{
    margin-left: 20px;
    font-size: 12px;
    color: red;
}


.modal-input-container{
    margin: 10px 20px;
}

.modal-input,
.modal-input :deep(input){
    width: 100%;
}

.modal-footer{
    display: flex;
    flex-wrap: wrap;
    justify-content:end;
}

</style>
