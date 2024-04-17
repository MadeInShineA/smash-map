<script setup>
import {useUserStore} from "../stores/UserStore.js";
import GoogleAddressAutocomplete from "vue3-google-address-autocomplete";
import Password from "primevue/password";
import MultiSelect from "primevue/multiselect";
import Checkbox from "primevue/checkbox";
import InputText from "primevue/inputtext";
import {onMounted, ref, watch} from "vue";
import {useOptionsStore} from "../stores/OptionsStore.js";
import {useAxios} from "@vueuse/integrations/useAxios";

const props = defineProps({
    darkMode: Boolean
})

const userStore = useUserStore()
const optionsStore = useOptionsStore()


// TODO Make the user connected == session user check here or inside router.js (beforeEnter) ?


const { data: characterOptions, isFinished: charactersFetched, execute: fetchCharacters } = useAxios('/api/characters', {}, {immediate: false})

const settings = ref()
userStore.getSettings(userStore.user.id, props.darkMode).then((response)=>{
    settings.value = response.data
    fetchCharacters({params: {games: settings.value.games.join(',')}})


    watch(() => settings.value.games, function(games){
        games = games.length > 0 ? games.join(',') : 'default'
        fetchCharacters({params: {games}})
    }, {immediate: false})

})


watch(characterOptions, function(availableCharacters, oldValue){
    if (settings.value.games.length === 0){
        settings.value.characters = []
    }

    if (oldValue && availableCharacters.length !== 0){
        let availableCharacterIds = []
        availableCharacters.data.forEach((game) => {
            game.characters.forEach((character) => {
                availableCharacterIds.push(character.id)
            })
        })

        settings.valuecharacters = settings.value.characters.filter((character) => {
            return availableCharacterIds.includes(character)
        })
    }
}, {immediate: false})


const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;


// TODO Check if it's done correctly and in a good way
function RegisterUserAddressInputSelect(place) {
    for (let i = 0; i < place.address_components.length; i++) {
        const addressType = place.address_components[i].types[0];
        if (addressType === "country") {
            registerUser.countryCode = place.address_components[i].short_name;
        }
    }
    settings.value.address.latitude = place.geometry.location.lat()
    settings.value.address.longitude = place.geometry.location.lng()
}

function setRegisterUserAddressNameToTheAddressInput(){
    const addressInput = document.getElementById('register-address')
    settings.value.address.latitude = null
    settings.value.address.longitude = null
    settings.value.address.countryCode = ''
    settings.value.address.name = addressInput.value
}


onMounted(function(){
    console.log('Settings Mounted')
})

</script>

<template>
    <template v-if="settings && charactersFetched">
        {{settings}}
        <div id="settings-inputs-container">
            <div class="p-float-label settings-input-container">
                <InputText id="register-username" class="setting-input" v-model="settings.username" required/>
                <label for="register-username">Username</label>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerUsernameError in registerValidationErrors.username" :key="registerUsernameError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerUsernameError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <div class="p-float-label settings-input-container">
                <InputText id="register-email" class="setting-input" v-model="settings.email"/>
                <label for="register-email">Email</label>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerEmailError in registerValidationErrors.email" :key="registerEmailError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerEmailError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <div class="p-float-label settings-input-container">
                <Password id="register-password" class="setting-input" v-model="settings.password" :feedback="false" toggleMask/>
                <label for="register-password">Password</label>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerPasswordError in registerValidationErrors.password" :key="registerPasswordError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerPasswordError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <div class="p-float-label settings-input-container">
                <Password id="register-password-confirmation" class="setting-input" v-model="settings.passwordConfirmation" :feedback="false" toggleMask/>
                <label for="register-password">Password Confirmation</label>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerPasswordConfirmationError in registerValidationErrors.passwordConfirmation" :key="registerPasswordConfirmationError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerPasswordConfirmationError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <div class="settings-input-container">
                <Checkbox v-model="settings.isModder" :binary="true"  input-id="register-is-moder"/>
                <label for="register-is-moder" class="ml-10"> I am a controller modder </label>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerIsModder in registerValidationErrors.isModder" :key="registerIsModder" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerIsModder}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="modal-input-container">
                <MultiSelect id="register-games" class="modal-input" v-model="settings.games" :options="optionsStore.gameOptions" optionLabel="name" optionValue="id" :maxSelectedLabels="3" placeholder="Games"/>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerGamesError in registerValidationErrors.games" :key="registerGamesError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerGamesError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="modal-input-container">
                <MultiSelect id="register-characters" class="modal-input" :disabled="settings.games.length === 0" :loading="!charactersFetched" v-model="settings.characters" :maxSelectedLabels="2" :options="characterOptions.data" optionLabel="name" optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" placeholder="Characters" showClear>
                    <template #option="slotProps">
                        <div class="character-option">
                            <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerCharactersError in registerValidationErrors.characters" :key="registerCharactersError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerCharactersError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
<!--            </div>-->

            <div class="p-float-label modal-input-container">
                <GoogleAddressAutocomplete
                    :apiKey="googleMapApiKey"
                    v-model="settings.address"
                    @callback="RegisterUserAddressInputSelect"
                    @keyup="setRegisterUserAddressNameToTheAddressInput"
                    id="register-address"
                    :class="{ 'p-filled': settings.addressName !== ''}"
                    class="p-inputtext p-component modal-input"
                />
                <label for="register-address">Address</label>
<!--            </div>-->
<!--            <div class="validation-errors">-->
<!--                <TransitionGroup name="errors">-->
<!--                    <template v-for="registerAddressNameError in registerValidationErrors.addressName" :key="registerAddressNameError" class="validation-errors">-->
<!--                        <div class="validation-error">{{registerAddressNameError}}</div>-->
<!--                    </template>-->
<!--                </TransitionGroup>-->
            </div>
        </div>
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<style scoped>
#settings-inputs-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;
}

.settings-input-container{
    min-width: 300px;
    margin: 10px 20px;
}

.setting-input,
.setting-input :deep(input){
    width: 100%;
}
</style>
