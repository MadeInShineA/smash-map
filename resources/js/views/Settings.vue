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
import Slider from "primevue/slider";
import Swal from "sweetalert2";

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true
    }
})

const userStore = useUserStore()
const optionsStore = useOptionsStore()

const settings = ref()
const { data: characterOptions, isFinished: charactersFetched, execute: fetchCharacters } = useAxios('/api/characters', {}, {immediate: false})


userStore.getSettings(props.darkMode).then((response)=>{
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
    address: [],
    isOnMap: [],
    notifications: [],
    distanceNotificationsRadius: [],
    attendeesNotificationsThresholds: [],
    timeNotificationsThresholds: []
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

async function saveSettings() {
    try {
        settingsValidationErrors.value = {
            username: [],
            email: [],
            password: [],
            passwordConfirmation: [],
            games: [],
            characters: [],
            isModder: [],
            address: [],
            isOnMap: [],
            notifications: [],
            distanceNotificationsRadius: [],
            attendeesNotificationsThresholds: [],
            timeNotificationsThresholds: []
        }

        let sentDistanceNotificationsRadius = settings.value.distanceNotificationsRadius
        if(!settings.value.notifications.includes('hasDistanceNotifications')){
            sentDistanceNotificationsRadius = null
        }

        let sentAttendeesNotificationsThresholds = settings.value.attendeesNotificationsThresholds
        if(!settings.value.notifications.includes('hasAttendeesNotifications')){
            sentAttendeesNotificationsThresholds = null
        }else if(typeof(settings.value.attendeesNotificationsThresholds) === 'string'){
            sentAttendeesNotificationsThresholds = settings.value.attendeesNotificationsThresholds.split(',')
        }

        let sentTimeNotificationsThresholds = settings.value.timeNotificationsThresholds

        if(!settings.value.notifications.includes('hasTimeNotifications')){
            sentTimeNotificationsThresholds = null
        }else if(typeof(settings.value.timeNotificationsThresholds) === 'string'){
            sentTimeNotificationsThresholds = settings.value.timeNotificationsThresholds.split(',')
        }

        const sentSettings = {
            username: settings.value.username,
            email: settings.value.email,
            password: settings.value.password,
            passwordConfirmation: settings.value.passwordConfirmation,
            games: settings.value.games,
            characters: settings.value.characters,
            isModder: settings.value.isModder,
            address: settings.value.address,
            isOnMap: settings.value.isOnMap,
            notifications: settings.value.notifications,
            distanceNotificationsRadius: sentDistanceNotificationsRadius,
            attendeesNotificationsThresholds: sentAttendeesNotificationsThresholds,
            timeNotificationsThresholds: sentTimeNotificationsThresholds
        }

         userStore.updateSettings(sentSettings).then(function (response){

            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            Swal.fire({
                title: 'Settings saved!',
                text: response.data.message,
                icon: 'success',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
        })

    } catch (error) {
        console.log(error)
        if (error.response && error.response.data.errors === undefined && error.response.data.message && error.response.status === 500) {
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
        } else if (error.response && error.response.data.errors) {
            settingsValidationErrors.value = error.response.data.errors
        } else {
            console.log(error)
        }
    }
}

const showAttendeesNotificationsThresholdsHelp = ref(false)

const showTimeNotificationsThresholdsHelp = ref(false)

function deleteAccount(){

    try{
        const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
        const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            background: alertBackground,
            color: alertColor
        }).then(async (result) => {
            if (result.isConfirmed) {
                await userStore.deleteAccount().then(async function (response) {
                    await Swal.fire({
                        title: 'Account deleted!',
                        text: response.data.message,
                        icon: 'success',
                        background: alertBackground,
                        color: alertColor,
                        timer: 2000,
                        showConfirmButton: false
                    })
                })
            }
        })
    }catch (error) {
        if ( error.response && error.response.data.errors === undefined && error.response.data.message && error.response.status === 500) {
            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            Swal.fire({
                title: 'Error',
                text: error.response.data.message,
                icon: 'error',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
        } else {
            console.log(error)
        }
    }
}

</script>

<template>
        <div  v-if="settings && characterOptions" id="settings-container">
            <div class="p-float-label setting-input-container">
                <InputText id="settings-username" class="setting-input" v-model="settings.username" required @click="settingsValidationErrors.username = []"/>
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
                <InputText id="settings-email" class="setting-input" v-model="settings.email" @click="settingsValidationErrors.email = []"/>
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
                <Password id="settings-password" class="setting-input" v-model="settings.password" :feedback="false" toggleMask @click="settingsValidationErrors.password = []"/>
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
                <Password id="settings-password-confirmation" class="setting-input" v-model="settings.passwordConfirmation" :feedback="false" toggleMask @click="settingsValidationErrors.passwordConfirmation = []"/>
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
                    <MultiSelect id="settings-games" class="setting-input" v-model="settings.games" :options="optionsStore.gameOptions" optionLabel="name" optionValue="id" :maxSelectedLabels="0" placeholder="Games" @click="settingsValidationErrors.games = []"/>
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
                    <MultiSelect id="settings-characters" class="setting-input" :disabled="settings.games.length === 0 || !charactersFetched" :loading="!charactersFetched" v-model="settings.characters" :maxSelectedLabels="0" :options="characterOptions.data" optionLabel="name" optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" placeholder="Characters" showClear @click="settingsValidationErrors.characters = []">
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
                <Checkbox v-model="settings.isModder" :binary="true"  input-id="settings-is-moder" @click="settingsValidationErrors.isModder = []"/>
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
                    @click="settingsValidationErrors.address = []"
                />
                <label for="settings-address">Address</label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsAddressError in settingsValidationErrors.address" :key="settingsAddressError" class="validation-errors">
                        <div class="validation-error">{{settingsAddressError}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="setting-input-container">
                <Checkbox v-model="settings.isOnMap" :binary="true"  input-id="settings-is-on-map" @click="settingsValidationErrors.isOnMap = []"/>
                <label for="settings-is-on-map" class="ml-10"> I want to appear on the map </label>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsIsOnMap in settingsValidationErrors.isOnMap" :key="settingsIsOnMap" class="validation-errors">
                        <div class="validation-error">{{settingsIsOnMap}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <div class="setting-input-container">
                <FloatLabel>
                    <MultiSelect class="setting-input" id="settings-notifications" :options="optionsStore.notificationOptions" v-model="settings.notifications" optionLabel="name" optionValue="value" :maxSelectedLabels="0" placeholder="Notifications" @click="settingsValidationErrors.notifications = []"/>
                    <label for="settings-notifications">Notifications</label>
                </FloatLabel>
            </div>
            <div class="validation-errors">
                <TransitionGroup name="errors">
                    <template v-for="settingsNotifications in settingsValidationErrors.notifications" :key="settingsNotifications" class="validation-errors">
                        <div class="validation-error">{{settingsNotifications}}</div>
                    </template>
                </TransitionGroup>
            </div>

            <template v-if="settings.notifications.includes('hasDistanceNotifications')">
                <div class="setting-input-container" >
                    <FloatLabel>
                        <InputText id="settings-distance-notifications-radius" class="setting-input" v-model.number="settings.distanceNotificationsRadius" @click="settingsValidationErrors.distanceNotificationsRadius = []"/>
                        <Slider class="setting-input" v-model="settings.distanceNotificationsRadius" :min="1" :max="2000" @click="settingsValidationErrors.distanceNotificationsRadius = []"/>
                        <label for="settings-distance-notifications-radius">Distance Notifications Radius (KM)</label>
                    </FloatLabel>
                </div>
                <div class="validation-errors">
                    <TransitionGroup name="errors">
                        <template v-for="settingsDistanceNotificationsRadius in settingsValidationErrors.distanceNotificationsRadius" :key="settingsDistanceNotificationsRadius" class="validation-errors">
                            <div class="validation-error">{{settingsDistanceNotificationsRadius}}</div>
                        </template>
                    </TransitionGroup>
                </div>
            </template>

            <template v-if="settings.notifications.includes('hasAttendeesNotifications')">
                <div class="setting-input-container" >
                    <FloatLabel>
                        <InputText id="attendees-notifications-thresholds" class="setting-input" v-model="settings.attendeesNotificationsThresholds" @click="settingsValidationErrors.attendeesNotificationsThresholds = []" />
                        <label for="attendees-notifications-thresholds">Attendees Notifications Thresholds</label>
                    </FloatLabel>
                </div>
                <div class="thresholds-help-container">
                    <small>
                        Thresholds must be separated by comas.
                        <i class="pi pi-question-circle thresholds-help-question-mark" @click="showAttendeesNotificationsThresholdsHelp =! showAttendeesNotificationsThresholdsHelp" />
                    </small>

                    <div v-if="showAttendeesNotificationsThresholdsHelp" class="thresholds-help-text-container">
                        <small>You will be notified when one of your followed event's attendee count reaches one of the threshold</small>
                    </div>
                </div>
                <div class="validation-errors">
                    <TransitionGroup name="errors">
                        <template v-for="settingsAttendeesNotificationsThresholds in settingsValidationErrors.attendeesNotificationsThresholds" :key="settingsAttendeesNotificationsThresholds" class="validation-errors">
                            <div class="validation-error">{{settingsAttendeesNotificationsThresholds}}</div>
                        </template>
                    </TransitionGroup>
                </div>
            </template>


            <template v-if="settings.notifications.includes('hasTimeNotifications')">
                <div class="setting-input-container">
                    <FloatLabel>
                        <InputText id="time-notifications-thresholds" class="setting-input" v-model="settings.timeNotificationsThresholds" @click="settingsValidationErrors.timeNotificationsThresholds = []" />
                        <label for="time-notifications-thresholds">Time Notifications Thresholds (days)</label>
                    </FloatLabel>
                </div>
                <div class="thresholds-help-container">
                    <small>
                        Thresholds must be separated by comas.
                        <i class="pi pi-question-circle thresholds-help-question-mark" @click="showTimeNotificationsThresholdsHelp =! showTimeNotificationsThresholdsHelp" />
                    </small>

                    <div v-if="showTimeNotificationsThresholdsHelp" class="thresholds-help-text-container">
                        <small>You will be notified when one of your followed event's day count reaches one of the threshold</small>
                    </div>
                </div>
                <div class="validation-errors">
                    <TransitionGroup name="errors">
                        <template v-for="settingsTimeNotificationsThresholds in settingsValidationErrors.timeNotificationsThresholds" :key="settingsTimeNotificationsThresholds" class="validation-errors">
                            <div class="validation-error">{{settingsTimeNotificationsThresholds}}</div>
                        </template>
                    </TransitionGroup>
                </div>
            </template>
            <div>
                <Button label="Delete account" severity="danger" icon="pi pi-trash" plain text @click="deleteAccount"></Button>
                <Button label="Save" severity="success" icon="pi pi-check" plain text @click="saveSettings"></Button>
            </div>
        </div>
    <LoaderComponent v-else></LoaderComponent>
</template>

<style scoped>
#settings-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 20px;
}

.setting-input-container{
    width: 300px;
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
    margin-top: 10px;
    min-height: 1em;
    width: 300px;
}

.validation-error{
    font-size: 12px;
    color: red;
}

.thresholds-help-container{
    width: 300px;
}

.thresholds-help-text-container{
    margin-top: 10px;
}

.thresholds-help-question-mark{
    cursor: pointer;
    color: #007ad9;
    vertical-align: middle;
}
</style>
