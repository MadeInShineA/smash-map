<script setup>

import Password from "primevue/password";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import {ref} from "vue";
import {useFiltersStore} from "../stores/FiltersStore.js";
import Dropdown from "primevue/dropdown";

const filtersStore = useFiltersStore()
const props = defineProps({
    darkMode: Boolean,
    showRegisterModal:Boolean
})

const emit = defineEmits(['switchShowRegisterModal'])

const registerUser = ref({
    username: '',
    email: '',
    password: '',
    mainGame: null
})

const mainGame = ref()

</script>

<template>
    <Dialog class="user-modal" :visible="showRegisterModal" @update:visible="emit('switchShowRegisterModal')" :draggable="false" modal header="Register">
        <div class="modal-inputs">
            <div class="p-float-label modal-input">
                <InputText id="register-username" v-model="registerUser.username" required />
                <label for="register-username">Username</label>
            </div>
            <div class="p-float-label modal-input">
                <InputText id="register-email" v-model="registerUser.email" />
                <label for="register-email">Email</label>
            </div>
            <div class="p-float-label modal-input">
                <Password id="register-password" v-model="registerUser.password" toggleMask />
                <label for="register-password">Password</label>
            </div>
            <div class="modal-input">
                <Dropdown v-model="registerUser.mainGame" :options="filtersStore.eventGameOptions" optionLabel="name" showClear placeholder="Main Game"/>
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" icon="pi pi-times" @click="emit('switchShowRegisterModal')" text plain/>
            <Button label="Register" icon="pi pi-check" @click="console.log('Register')" text plain/>
        </template>
    </Dialog>
</template>

<style scoped>

.modal-input{
    margin-left: 20px;
    margin-bottom: 10px;
    margin-top: 10px
}

</style>
