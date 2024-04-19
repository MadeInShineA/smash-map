<script setup>
import {useRoute} from "vue-router";
import InputText from "primevue/inputtext";
import {ref} from "vue";
import Password from "primevue/password";
import Button from "primevue/button";
import {useUserStore} from "../stores/UserStore.js";
import Swal from "sweetalert2";
import FloatLabel from "primevue/floatlabel";

const props = defineProps({
    token: String,
    darkMode: Boolean

})

const route = useRoute()
const email = ref(route.query.email)
const newPassword = ref('')
const newPasswordConfirmation = ref('')
const resetPasswordErrors = ref({
    email: [],
    password: [],
    passwordConfirmation: []
})

const userStore = useUserStore()

async function resetPassword() {
    try {
        await userStore.resetPassword({
            email: email.value,
            password: newPassword.value,
            passwordConfirmation: newPasswordConfirmation.value,
            token: props.token
        }).then(async (response) => {
            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            await Swal.fire({
                title: 'Password reset!',
                text: response.data.message,
                icon: 'success',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
            window.location.href = '/'
        })
    } catch (error) {
        if (error.response.data.errors === undefined && error.response.data.message &&  error.response.status === 500) {
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
        } else if (error.response.data.errors) {
            resetPasswordErrors.value = error.response.data.errors
        } else {
            console.log(error)
        }
    }

}


</script>

<template>
    <div id="inputs-container">
        <div class="input-errors-container">
            <div class="input-container">
                <FloatLabel>
                    <InputText id="email" class="input" v-model="email" @click="resetPasswordErrors.email=[]"/>
                    <label for="email">Email</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="emailError in resetPasswordErrors.email" :key="emailError" class="validation-errors">
                        <div class="validation-error">{{emailError}}</div>
                    </template>
                </TransitionGroup>
            </div>
        </div>


        <div class="input-errors-container">
            <div class="input-container">
                <FloatLabel>
                    <Password id="new-password" class="input" v-model="newPassword" toggle-mask :feedback="false" @click="resetPasswordErrors.password=[]"/>
                    <label for="new-password">New password</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="passwordError in resetPasswordErrors.password" :key="passwordError" class="validation-errors">
                        <div class="validation-error">{{passwordError}}</div>
                    </template>
                </TransitionGroup>
            </div>
        </div>

        <div class="input-errors-container">
            <div class="input-container">
                <FloatLabel>
                    <Password id="new-password-confirmation" class="input" v-model="newPasswordConfirmation" toggle-mask :feedback="false" @click="resetPasswordErrors.passwordConfirmation=[]"/>
                    <label for="new-password">New password confirmation</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="passwordError in resetPasswordErrors.passwordConfirmation" :key="passwordError" class="validation-errors">
                        <div class="validation-error">{{passwordError}}</div>
                    </template>
                </TransitionGroup>
            </div>
        </div>

        <div id="reset-password-button">
            <Button label="Reset password" severity="success" icon="pi pi-check" @click="resetPassword" text plain/>
        </div>

    </div>
</template>

<style scoped>

#inputs-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;
}

.input-container{
    min-width: 300px;
    margin: 10px 20px;
}

.input,
.input :deep(input){
    width: 100%;
}

.errors-enter-active,
.errors-leave-active {
    transition: opacity 0.5s ease;}
.errors-enter-from,
.errors-leave-to {
    opacity: 0;
}

.validation-errors {
    min-height: 1em;

}


.validation-error{
    font-size: 12px;
    color: red;
    margin-left: 20px;
}

#reset-password-button{
    margin-top: 10px;
}


</style>
