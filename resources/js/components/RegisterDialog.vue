<script setup>

import Password from "primevue/password";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import {reactive, ref, watch} from "vue";
import {useFiltersStore} from "../stores/FiltersStore.js";
import Dropdown from "primevue/dropdown";

const filtersStore = useFiltersStore()
const props = defineProps({
    darkMode: Boolean,
    showRegisterModal:Boolean
})

const emit = defineEmits(['switchShowRegisterModal'])

const registerUser = reactive({
    username: '',
    email: '',
    password: '',
    mainGame: null,
    mainCharacter: null
})

const mainGameOptions = ref([
    {name: '64', value: '4'},
    {name: 'Melee', value: '1'},
    {name: 'Brawl', value: '5'},
    {name: 'Project M', value: '2'},
    {name: 'Project +', value: '33602'},
    {name: '3DS / WiiU', value: '3'},
    {name: 'Ultimate', value: '1386'},
])

const mainCharacterOptions = ref([])

const fetchingCharacters = ref(false)
watch(() => registerUser.mainGame, async (mainGame) => {
    if (mainGame) {
        registerUser.mainCharacter = null
        fetchingCharacters.value = true
        const response = await axios.get('/api/characters?game=' + mainGame.value)
        mainCharacterOptions.value = response.data.data
        fetchingCharacters.value = false
    }
});

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
                <Dropdown v-model="registerUser.mainGame" :options="mainGameOptions" optionLabel="name" showClear placeholder="Main Game"/>
            </div>
            <div class="modal-input">
                <Dropdown v-if="registerUser.mainGame && !fetchingCharacters" v-model="registerUser.mainCharacter" :options="mainCharacterOptions" optionLabel="name" showClear filter placeholder="Main Character">
                    <template #option="slotProps">
                        <div class="character-option">
                            <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </Dropdown>
                <Dropdown v-else-if="fetchingCharacters" loading placeholder="Main Character"></Dropdown>
                <Dropdown v-else disabled placeholder="Main Character" ></Dropdown>
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

.character-option{
    display: flex;
}

.character-option-image{
    width: 18px;
    margin-right: 5px;
}

</style>
