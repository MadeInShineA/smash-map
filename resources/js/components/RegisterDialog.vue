<script setup>

import Password from "primevue/password";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import {onMounted, reactive, ref, watch} from "vue";
import Dropdown from "primevue/dropdown";
import GoogleAddressAutocomplete from 'vue3-google-address-autocomplete'
import Swal from "sweetalert2";

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
    mainGame: null,
    mainCharacter: null,
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
    mainGame: [],
    mainCharacter: [],
    addressName: [],
    register: []
})

const mainGameOptions = ref([
    {name: '64', id: '4'},
    {name: 'Melee', id: '1'},
    {name: 'Brawl', id: '5'},
    {name: 'Project M', id: '2'},
    {name: 'Project +', id: '33602'},
    {name: '3DS / WiiU', id: '3'},
    {name: 'Ultimate', id: '1386'},
])

const mainCharacterOptions = ref([])

const fetchingCharacters = ref(false)
watch(() => registerUser.mainGame, async (mainGame) => {
    if (mainGame) {
        registerUser.mainCharacter = null
        fetchingCharacters.value = true
        console.log(mainGame)
        const response = await axios.get('/api/characters?game=' + mainGame)
        mainCharacterOptions.value = response.data.data
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
            localStorage.setItem('userData', JSON.stringify(response.data.data.user));
            localStorage.setItem('accessToken', response.data.data.token)
            localStorage.setItem('tokenTime', new Date().toString());
            // registerUser.username = ''
            // registerUser.password = ''
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
    <Dialog class="user-modal" :visible="showRegisterModal" @update:visible="emit('switchShowRegisterModal')" :draggable="false" modal header="Register">
        <div class="modal-inputs">
            <div class="p-float-label modal-input">
                <InputText id="register-username" v-model="registerUser.username" required @focus="registerValidationErrors.username = []" />
                <label for="register-username">Username</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerUsernameError in registerValidationErrors.username" :key="registerUsernameError" class="validation-errors">
                        <div class="validation-error">{{registerUsernameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input">
                <InputText id="register-email" v-model="registerUser.email" @focus="registerValidationErrors.email = []"/>
                <label for="register-email">Email</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerEmailError in registerValidationErrors.email" :key="registerEmailError" class="validation-errors">
                        <div class="validation-error">{{registerEmailError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input">
                <Password id="register-password" v-model="registerUser.password" toggleMask @focus="registerValidationErrors.password = []" />
                <label for="register-password">Password</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerPasswordError in registerValidationErrors.password" :key="registerPasswordError" class="validation-errors">
                        <div class="validation-error">{{registerPasswordError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input">
                <Password id="register-password-confirmation" v-model="registerUser.passwordConfirmation" :feedback="false" toggleMask @focus="registerValidationErrors.passwordConfirmation = []" />
                <label for="register-password">Password Confirmation</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerPasswordConfirmationError in registerValidationErrors.passwordConfirmation" :key="registerPasswordConfirmationError" class="validation-errors">
                        <div class="validation-error">{{registerPasswordConfirmationError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input">
                <Dropdown v-model="registerUser.mainGame" :options="mainGameOptions" optionLabel="name" optionValue="id" showClear placeholder="Main Game" @focus="registerValidationErrors.mainGame = []"/>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerMainGameError in registerValidationErrors.mainGame" :key="registerMainGameError" class="validation-errors">
                        <div class="validation-error">{{registerMainGameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input">
                <Dropdown v-if="registerUser.mainGame && !fetchingCharacters" v-model="registerUser.mainCharacter" :options="mainCharacterOptions" optionLabel="name" optionValue="id" showClear filter placeholder="Main Character" @focus="registerValidationErrors.mainCharacter = []">
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
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerMainCharacterError in registerValidationErrors.mainCharacter" :key="registerMainCharacterError" class="validation-errors">
                        <div class="validation-error">{{registerMainCharacterError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label modal-input">
                <GoogleAddressAutocomplete
                    :apiKey="googleMapApiKey"
                    v-model="registerUser.addressName"
                    @callback="RegisterUserAddressInputSelect"
                    @keyup="setRegisterUserAddressNameToTheAddressInput"
                    @focus="registerValidationErrors.addressName = []"
                    id="register-address"
                    :class="{ 'p-filled': registerUser.addressName !== ''}"
                    class="p-inputtext p-component"
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
