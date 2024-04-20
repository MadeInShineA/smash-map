<script setup>

import Password from "primevue/password";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import {onMounted, reactive, ref, watch} from "vue";
import GoogleAddressAutocomplete from 'vue3-google-address-autocomplete'
import Swal from "sweetalert2";
import MultiSelect from "primevue/multiselect";
import Checkbox from "primevue/checkbox";
import {useUserStore} from "../stores/UserStore.js";
import {useOptionsStore} from "../stores/OptionsStore.js";
import {useAxios} from "@vueuse/integrations/useAxios";
import FloatLabel from "primevue/floatlabel";

const userStore = useUserStore()
const optionsStore = useOptionsStore()

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
    isModder: false,
    isOnMap: false
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

const { data: characterOptions, isFinished: charactersFetched, execute: fetchCharacters } = useAxios('/api/characters', {}, {immediate: false})
fetchCharacters({params: {setup: true}})

watch(() => registerUser.games, function(games){
    games = games.length > 0 ? games.join(',') : 'default'
    fetchCharacters({params: {games}})
}, {immediate: false})

watch(characterOptions, function(availableCharacters, oldValue){
    if (registerUser.games.length === 0){
        registerUser.characters = []
    }

    if (oldValue && availableCharacters.length !== 0){
        let availableCharacterIds = []
        availableCharacters.data.forEach((game) => {
            game.characters.forEach((character) => {
                availableCharacterIds.push(character.id)
            })
        })

    registerUser.characters = registerUser.characters.filter((character) => {
        return availableCharacterIds.includes(character)
    })
    }
}, {immediate: false})


const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;

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

    axios.get('/sanctum/csrf-cookie').then(async () => {
        try {
            await userStore.register(registerUser).then(async function (response) {
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
                registerUser.isModder = false

                emit('switchShowRegisterModal')
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
            if(error.response.data.errors === undefined && error.response.data.message && error.response.status === 500){
                emit('switchShowRegisterModal')
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
                registerValidationErrors.value = error.response.data.errors
            }else {
                console.log(error)
            }
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
        <div  v-if="characterOptions">
            <div class="modal-input-container">
                <FloatLabel>
                    <InputText id="register-username" class="modal-input" v-model="registerUser.username" required  autofocus @focus="registerValidationErrors.username = []" />
                    <label for="register-username">Username</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerUsernameError in registerValidationErrors.username" :key="registerUsernameError" class="validation-errors">
                        <div class="validation-error">{{registerUsernameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <FloatLabel>
                    <InputText id="register-email" class="modal-input" v-model="registerUser.email" @focus="registerValidationErrors.email = []"/>
                    <label for="register-email">Email</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerEmailError in registerValidationErrors.email" :key="registerEmailError" class="validation-errors">
                        <div class="validation-error">{{registerEmailError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <FloatLabel>
                    <Password id="register-password" class="modal-input" v-model="registerUser.password" :feedback="false" toggleMask @focus="registerValidationErrors.password = []" />
                    <label for="register-password">Password</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerPasswordError in registerValidationErrors.password" :key="registerPasswordError" class="validation-errors">
                        <div class="validation-error">{{registerPasswordError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <FloatLabel>
                    <Password id="register-password-confirmation" class="modal-input" v-model="registerUser.passwordConfirmation" :feedback="false" toggleMask @focus="registerValidationErrors.passwordConfirmation = []" />
                    <label for="register-password">Password Confirmation</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerPasswordConfirmationError in registerValidationErrors.passwordConfirmation" :key="registerPasswordConfirmationError" class="validation-errors">
                        <div class="validation-error">{{registerPasswordConfirmationError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="modal-input-container">
                <FloatLabel>
                    <MultiSelect id="register-games" class="modal-input" v-model="registerUser.games" :options="optionsStore.gameOptions" optionLabel="name" optionValue="id" :maxSelectedLabels="3" placeholder="Games" @focus="registerValidationErrors.games = []"/>
                    <label for="register-games">Games</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerGamesError in registerValidationErrors.games" :key="registerGamesError" class="validation-errors">
                        <div class="validation-error">{{registerGamesError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <FloatLabel>
                    <MultiSelect id="register-characters" class="modal-input" :disabled="registerUser.games.length === 0" :loading="!charactersFetched" v-model="registerUser.characters" :maxSelectedLabels="2" :options="characterOptions.data" optionLabel="name"  optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" placeholder="Characters" showClear @focus="registerValidationErrors.characters = []">
                        <template #option="slotProps">
                            <div class="character-option">
                                <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                                <div>{{ slotProps.option.name }}</div>
                            </div>
                        </template>
                    </MultiSelect>
                    <label for="register-characters">Characters</label>
                </FloatLabel>

            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerCharactersError in registerValidationErrors.characters" :key="registerCharactersError" class="validation-errors">
                        <div class="validation-error">{{registerCharactersError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <Checkbox v-model="registerUser.isModder" :binary="true"  input-id="register-is-moder"/>
                <label for="register-is-moder" class="ml-10"> I am a controller modder </label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerIsModder in registerValidationErrors.isModder" :key="registerIsModder" class="validation-errors">
                        <div class="validation-error">{{registerIsModder}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <FloatLabel>
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
                </FloatLabel>

            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerAddressNameError in registerValidationErrors.addressName" :key="registerAddressNameError" class="validation-errors">
                        <div class="validation-error">{{registerAddressNameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="modal-input-container">
                <Checkbox v-model="registerUser.isOnMap" :binary="true"  input-id="register-is-on-map"/>
                <label for="register-is-on-map" class="ml-10"> I want to appear on the map </label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="registerIsOnMap in registerValidationErrors.isOnMap" :key="registerIsOnMap" class="validation-errors">
                        <div class="validation-error">{{registerIsOnMap}}</div>
                    </template>
                </TransitionGroup>
            </div>

        </div>
        <template v-else>
            <LoaderComponent></LoaderComponent>
        </template>
        <template #footer>
            <Button label="Cancel" severity="danger" icon="pi pi-times" @click="emit('switchShowRegisterModal')" text plain/>
            <Button label="Register" severity="success" icon="pi pi-check" @click="register" text plain/>
        </template>
    </Dialog>
</template>


<style>
#register-modal .p-dialog-title{
    margin: auto;
}

.pac-container{
    z-index: 10000;
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

.ml-10{
    margin-left: 10px;
}


</style>
