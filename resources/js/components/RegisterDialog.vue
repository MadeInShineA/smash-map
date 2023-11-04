<script setup>

import Password from "primevue/password";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import {onMounted, reactive, ref, watch} from "vue";
import Dropdown from "primevue/dropdown";
import GoogleAddressAutocomplete from 'vue3-google-address-autocomplete'
import Swal from "sweetalert2";
import MultiSelect from "primevue/multiselect";

const props = defineProps({
    darkMode: Boolean,
    showRegisterModal:Boolean
})

const emit = defineEmits(['switchShowRegisterModal'])

const registerUser = reactive({
    username: '',
    email: '',
    password: '',
    passwordConfirmation: '',
    games: [],
    characters: [],
    addressName: '',
    latitude: null,
    longitude: null,
    countryCode: '',
})

const registerValidationErrors = ref({
    username: [],
    email: [],
    password: [],
    passwordConfirmation: [],
    games: [],
    characters: [],
    addressName: [],
    register: []
})

const gameOptions = ref([
    {name: '64', id: '4'},
    {name: 'Melee', id: '1'},
    {name: 'Brawl', id: '5'},
    {name: 'Project M', id: '2'},
    {name: 'Project +', id: '33602'},
    {name: '3DS / WiiU', id: '3'},
    {name: 'Ultimate', id: '1386'},
])

const charactersOptions = ref([])

const fetchingCharacters = ref(false)
watch(() => registerUser.games, async (games) => {
    if (games) {
        fetchingCharacters.value = true
        const response = await axios.get('/api/characters?games=' + games)
        charactersOptions.value = response.data.data


        const characterOptionsId = []
        charactersOptions.value.forEach((game) => {
            game.characters.forEach((character) => {
                characterOptionsId.push(character.id)
            })
        })
        registerUser.characters = registerUser.characters.filter((character) => {
            return characterOptionsId.includes(character)
        })

        fetchingCharacters.value = false


    }
});

const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;

const RegisterUserAddressInput = ref(null)

function RegisterUserAddressInputSelect(place) {
    for (let i = 0; i < place.address_components.length; i++) {
        const addressType = place.address_components[i].types[0];
        if (addressType === "country") {
            registerUser.countryCode = place.address_components[i].short_name;
        }
    }
    registerUser.latitude = place.geometry.location.lat()
    registerUser.longitude = place.geometry.location.lng()
}

function setRegisterUserAddressNameToTheAddressInput(){
    const addressInput = document.getElementById('register-address')
    registerUser.latitude = null
    registerUser.longitude = null
    registerUser.countryCode = ''
    registerUser.addressName = addressInput.value
}

function register(){
    registerValidationErrors.value.register = []

    const header = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    }

    axios.get('/sanctum/csrf-cookie').then(async () => {
        try {
            const response = await axios.post('/api/register', registerUser, header)
            registerUser.username = ''
            registerUser.password = ''
            registerUser.passwordConfirmation = ''
            registerUser.email = ''
            registerUser.games = []
            registerUser.characters = []
            registerUser.addressName = ''
            registerUser.latitude = null
            registerUser.longitude = null
            registerUser.countryCode = ''
            localStorage.setItem('userData', JSON.stringify(response.data.data.user));
            localStorage.setItem('accessToken', response.data.data.token)
            localStorage.setItem('tokenTime', new Date().toString());
            emit('setUser')
            emit('switchShowRegisterModal')
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
            console.log(error.response.data.errors)
            registerValidationErrors.value = error.response.data.errors
        }
    })
}

onMounted(function(){
    console.log('RegisterDialog Mounted')
})
</script>

<template>
    <Dialog id="register-modal" class="user-modal" :visible="showRegisterModal" @update:visible="emit('switchShowRegisterModal')" :draggable="false" modal header="Register" :style="{ width: '30vw' }" :breakpoints="{ '1200px': '50vw', '575px': '90vw' }">
        <template #header class="p-dialog-title p-dialog-header margin-auto"></template>
        <div class="modal-inputs">
            <div class="p-float-label modal-input-container">
                <InputText id="register-username" class="modal-input" v-model="registerUser.username" required @focus="registerValidationErrors.username = []" />
                <label for="register-username">Username</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerUsernameError in registerValidationErrors.username" :key="registerUsernameError" class="validation-errors">
                        <div class="validation-error">{{registerUsernameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input-container">
                <InputText id="register-email" class="modal-input" v-model="registerUser.email" @focus="registerValidationErrors.email = []"/>
                <label for="register-email">Email</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerEmailError in registerValidationErrors.email" :key="registerEmailError" class="validation-errors">
                        <div class="validation-error">{{registerEmailError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input-container">
                <Password id="register-password" class="modal-input" v-model="registerUser.password" :feedback="false" toggleMask @focus="registerValidationErrors.password = []" />
                <label for="register-password">Password</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerPasswordError in registerValidationErrors.password" :key="registerPasswordError" class="validation-errors">
                        <div class="validation-error">{{registerPasswordError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input-container">
                <Password id="register-password-confirmation" class="modal-input" v-model="registerUser.passwordConfirmation" :feedback="false" toggleMask @focus="registerValidationErrors.passwordConfirmation = []" />
                <label for="register-password">Password Confirmation</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerPasswordConfirmationError in registerValidationErrors.passwordConfirmation" :key="registerPasswordConfirmationError" class="validation-errors">
                        <div class="validation-error">{{registerPasswordConfirmationError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="modal-input-container p-float-label">
                <MultiSelect id="register-games" class="modal-input" v-model="registerUser.games" :options="gameOptions" optionLabel="name" optionValue="id" :maxSelectedLabels="2" showClear @focus="registerValidationErrors.games = []"/>
                <label for="register-games">Games</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerGamesError in registerValidationErrors.games" :key="registerGamesError" class="validation-errors">
                        <div class="validation-error">{{registerGamesError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="modal-input-container p-float-label">
                <MultiSelect id="register-characters" class="modal-input" v-if="registerUser.games && registerUser.games.length !== 0 && !fetchingCharacters" v-model="registerUser.characters" :maxSelectedLabels="2" :options="charactersOptions" optionLabel="name"  optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" showClear @focus="registerValidationErrors.characters = []">
                    <template #option="slotProps">
                        <div class="character-option">
                            <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
                <MultiSelect id="register-characters" class="modal-input" v-else-if="fetchingCharacters" loading></MultiSelect>
                <MultiSelect id="register-characters" class="modal-input" v-else disabled></MultiSelect>
                <label for="register-characters">Characters</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registercharactersError in registerValidationErrors.characters" :key="registercharactersError" class="validation-errors">
                        <div class="validation-error">{{registercharactersError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input-container">
                <GoogleAddressAutocomplete
                    :apiKey="googleMapApiKey"
                    v-model="registerUser.addressName"
                    @callback="RegisterUserAddressInputSelect"
                    @keyup="setRegisterUserAddressNameToTheAddressInput"
                    @focus="registerValidationErrors.addressName = []"
                    id="register-address"
                    :class="{ 'p-filled': registerUser.addressName !== ''}"
                    class="p-inputtext p-component modal-input"
                />
                <label for="register-address">Address</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerAddressNameError in registerValidationErrors.addressName" :key="registerAddressNameError" class="validation-errors">
                        <div class="validation-error">{{registerAddressNameError}}</div>
                    </template>
                </TransitionGroup>
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" icon="pi pi-times" @click="emit('switchShowRegisterModal')" text plain/>
            <Button label="Register" icon="pi pi-check" @click="register" text plain/>
        </template>
    </Dialog>
</template>


<style>

#register-modal .p-dialog-title{
    margin: auto;
}

// Style for the google map address autocomplete
.pac-container{
    z-index: 10000;
    cursor: pointer;
}

</style>

<style scoped>

.modal-input-container{
    margin: 10px 20px;
}

.modal-input,
.modal-input :deep(input){
    width: 100%;
}

.character-option{
    display: flex;
}

.character-option-image{
    width: 18px;
    margin-right: 5px;
}

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


</style>
