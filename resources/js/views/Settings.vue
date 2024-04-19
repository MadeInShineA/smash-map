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
import Button from "primevue/button";
import FloatLabel from "primevue/floatlabel";

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

const settingsValidationErrors = ref({
    username: [],
    email: [],
    password: [],
    passwordConfirmation: [],
    games: [],
    characters: [],
    isModder: [],
    addressName: [],
    notifications: []
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

        settings.value.characters = settings.value.characters.filter((character) => {
            return availableCharacterIds.includes(character)
        })
    }
}, {immediate: false})


const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;


// TODO Check if it's done correctly and in a good way
function addressInputSelect(place) {
    for (let i = 0; i < place.address_components.length; i++) {
        const addressType = place.address_components[i].types[0];
        if (addressType === "country") {
            settings.value.address.countryCode = place.address_components[i].short_name;
        }
    }
    settings.value.address.latitude = place.geometry.location.lat()
    settings.value.address.longitude = place.geometry.location.lng()
}

function addressNameToTheAddressInput(){
    const addressInput = document.getElementById('settings-address')
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
<!--    <template v-if="settings && characterOptions">-->
<!--        {{settings}}-->
        <div  v-if="settings && characterOptions" id="settings-container">
            <div class="p-float-label setting-input-container">
                <InputText id="settings-username" class="setting-input" v-model="settings.username" required/>
                <label for="settings-username">Username</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsUsernameError in settingsValidationErrors.username" :key="settingsUsernameError" class="validation-errors">
                        <div class="validation-error">{{settingsUsernameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label setting-input-container">
                <InputText id="settings-email" class="setting-input" v-model="settings.email"/>
                <label for="settings-email">Email</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsEmailError in settingsValidationErrors.email" :key="settingsEmailError" class="validation-errors">
                        <div class="validation-error">{{settingsEmailError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label setting-input-container">
                <Password id="settings-password" class="setting-input" v-model="settings.password" :feedback="false" toggleMask/>
                <label for="settings-password">New password</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsPasswordError in settingsValidationErrors.password" :key="settingsPasswordError" class="validation-errors">
                        <div class="validation-error">{{settingsPasswordError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label setting-input-container">
                <Password id="settings-password-confirmation" class="setting-input" v-model="settings.passwordConfirmation" :feedback="false" toggleMask/>
                <label for="settings-password">New password confirmation</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsPasswordConfirmationError in settingsValidationErrors.passwordConfirmation" :key="settingsPasswordConfirmationError" class="validation-errors">
                        <div class="validation-error">{{settingsPasswordConfirmationError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="setting-input-container">
                <FloatLabel>
                    <MultiSelect id="settings-games" class="setting-input" v-model="settings.games" :options="optionsStore.gameOptions" optionLabel="name" optionValue="id" :maxSelectedLabels="3" placeholder="Games"/>
                    <label for="settings-games">Games</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsGamesError in settingsValidationErrors.games" :key="settingsGamesError" class="validation-errors">
                        <div class="validation-error">{{settingsGamesError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <!-- TODO Fix the placeholder / empty item bug -->
            <div class="setting-input-container">
                <FloatLabel>
                    <MultiSelect id="settings-characters" class="setting-input" :disabled="settings.games.length === 0" :loading="!charactersFetched" v-model="settings.characters" :maxSelectedLabels="2" :options="characterOptions.data" optionLabel="name" optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" placeholder="Characters" showClear>
                        <template #option="slotProps">
                            <div class="character-option">
                                <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                                <div>{{ slotProps.option.name }}</div>
                            </div>
                        </template>
                    </MultiSelect>
                    <label for="settings-characters">Characters</label>
                </FloatLabel>

            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsCharactersError in settingsValidationErrors.characters" :key="settingsCharactersError" class="validation-errors">
                        <div class="validation-error">{{settingsCharactersError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="setting-input-container">
                <Checkbox v-model="settings.isModder" :binary="true"  input-id="settings-is-moder"/>
                <label for="settings-is-moder" class="ml-10"> I am a controller modder </label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsIsModder in settingsValidationErrors.isModder" :key="settingsIsModder" class="validation-errors">
                        <div class="validation-error">{{settingsIsModder}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="p-float-label setting-input-container">
                <GoogleAddressAutocomplete
                    :apiKey="googleMapApiKey"
                    v-model="settings.address.name"
                    :value="settings.address.name"
                    @callback="addressInputSelect"
                    @keyup="addressNameToTheAddressInput"
                    id="settings-address"
                    :class="{ 'p-filled': settings.address.name !== ''}"
                    class="p-inputtext p-component setting-input"
                />
                <label for="settings-address">Address</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsAddressNameError in settingsValidationErrors.addressName" :key="settingsAddressNameError" class="validation-errors">
                        <div class="validation-error">{{settingsAddressNameError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="setting-input-container">
                <Checkbox v-model="settings.isOnMap" :binary="true"  input-id="settings-is-on-map"/>
                <label for="settings-is-on-map" class="ml-10"> I want to appear on the map </label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsIsModder in settingsValidationErrors.isModder" :key="settingsIsModder" class="validation-errors">
                        <div class="validation-error">{{settingsIsModder}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="setting-input-container">
                <FloatLabel>
                    <MultiSelect id="settings-notifications" :options="optionsStore.notificationOptions" v-model="settings.notifications" optionLabel="name" optionValue="value" :maxSelectedLabels="3" placeholder="Notifications"/>
                    <label for="settings-notifications">Notifications</label>
                </FloatLabel>

            </div>

            <div>
                <Button label="Save" severity="success" icon="pi pi-check" plain text></Button>
            </div>
        </div>
    <LoaderComponent v-else></LoaderComponent>

<!--    <template v-else>-->
<!--        <LoaderComponent></LoaderComponent>-->
<!--    </template>-->
</template>

<style scoped>
#settings-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;
}

.setting-input-container{
    min-width: 300px;
    margin: 10px 20px;
}

.setting-input,
.setting-input :deep(input){
    width: 100%;
}

.setting-input{
    display: flex;
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
