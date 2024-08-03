<script>
export default { name:'map'}
</script>

<script setup>
import {GoogleMap, CustomControl, Marker, MarkerCluster, InfoWindow ,Circle} from "vue3-google-map";
import {nextTick, onActivated, onMounted, ref, watch} from "vue";
import AddressFilterSidebar from "@/components/AddressFilterSidebar.vue";
import Button from "primevue/button";
import LoaderComponent from "@/components/LoaderComponent.vue";
import Sidebar from "primevue/sidebar";
import Chip from "primevue/chip";
import Tag from "primevue/tag";
import { useAddressFiltersStore} from "../stores/AddressFiltersStore.js";
import { useAddressesStore } from '../stores/AddressesStore.js'
import { useEventsStore } from "../stores/EventsStore.js";
import { useUserStore } from "../stores/UserStore.js";
import FloatLabel from "primevue/floatlabel";
import Slider from "primevue/slider";
import Swal from "sweetalert2";
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    responsiveMenuDisplayed: {
        type: Boolean,
        required: true
    },
    darkMode: {
        type: Boolean,
        required: true
    },
    lat: {
        type: String,
        required: false
    },
    lng: {
        type: String,
        required: false
    }
})

const addressStore = useAddressesStore()

const eventsStore = useEventsStore()

const userStore = useUserStore()

const addressFiltersStore = useAddressFiltersStore()

const sideBarVisible = ref(false)

const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}

const center = ref(userStore.user.data.settings.address)




const circleCenter = ref(userStore.user.data.settings.address)
const distanceNotificationsRadius = ref(userStore.user.data.settings.distanceNotificationsRadius)
const isCircleVisible = ref(userStore.user.data.settings.hasDistanceNotifications)


watch(() => userStore.user.data, (value) => {
    circleCenter.value = value.settings.address
    distanceNotificationsRadius.value = value.settings.distanceNotificationsRadius
    isCircleVisible.value = value.settings.hasDistanceNotifications
}, {immediate: false})

const circleRef = ref()

watch(distanceNotificationsRadius, (value) => {
    circleParameters.value.radius = value * 1000
    circleRef.value.circle.setRadius(value * 1000)
}, {immediate: false})

const zoom = ref(4);
const infoWindows = ref([]);
const mapRef = ref();
const openedInfoWindowIndex = ref()

if(props.lat && props.lng){
    center.value = {lat: parseFloat(props.lat), lng: parseFloat(props.lng)}
    zoom.value = 10
}

const openInfoWindow = (i) => {
    openedInfoWindowIndex.value = i
    infoWindows.value.forEach(
        (ref, index) => {
            if (index !== i) {
                ref.infoWindow.close();
            }
        })
    circleInfoWindowRef.value.infoWindow.close()
};

const circleInfoWindowRef = ref()

function clickCircleEvent (i){
    openInfoWindow(i)
    circleInfoWindowRef.value.infoWindow.open(mapRef.value.map)
    circleInfoWindowRef.value.infoWindow.setPosition(circleCenter.value)
}

const circleParameters = ref({
    center: circleCenter,
    fillColor: 'aqua',
    radius: distanceNotificationsRadius.value * 1000,
    clickable: true,
    visible: isCircleVisible
})

//TODO Fix the Zooming on marker click

const legendsVisible = ref(false)

const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;

const validationErrors = ref({
    distanceNotificationsRadius: []
})

async function updateDistanceNotificationsRadius() {
    try {
        validationErrors.value.distanceNotificationsRadius = []
        await userStore.updateDistanceNotificationsRadius(distanceNotificationsRadius.value).then(async function (response){
            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            await Swal.fire({
                title: 'Distance notifications radius saved!',
                text: response.data.message,
                icon: 'success',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
        })

    }catch (error) {
        console.log(error)
        if (error.response.data.errors === undefined && error.response.data.message && error.response.status === 500) {
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
            validationErrors.value = error.response.data.errors
        } else {
            console.log(error)
        }
    }
}

onMounted(()=>{
    console.log('Map Mounted')
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            center.value = {lat: position.coords.latitude, lng: position.coords.longitude};
            zoom.value = 10;
        })
    }
    watch(() => addressStore.addressesFetched, () => {
        infoWindows.value = [];
        if (mapRef.value) {
            center.value = mapRef.value.map.getCenter()
            zoom.value = mapRef.value.map.getZoom()
        }
    }, {immediate: false})
})

onActivated(() => {
    center.value = userStore.user.data.settings.address
    circleCenter.value = userStore.user.data.settings.address
    distanceNotificationsRadius.value = userStore.user.data.settings.distanceNotificationsRadius
    isCircleVisible.value = userStore.user.data.settings.hasDistanceNotifications

    if(props.lat && props.lng){
        center.value = {lat: parseFloat(props.lat), lng: parseFloat(props.lng)}
        zoom.value = 10
        if(mapRef.value){
            mapRef.value.map.setCenter(center.value)
            mapRef.value.map.setZoom(zoom.value)
        }
    }})


</script>

<template>
    <AddressFilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></AddressFilterSidebar>
    <template v-if="addressStore.addressesFetched">
        <GoogleMap ref="mapRef" :api-key="googleMapApiKey" language="en" :map-type-control-options="{ mapTypeIds: ['roadmap','satellite',]}" style="width: 100%; height: 100%" :center="center" :zoom="zoom" :min-zoom="4" @click="openedInfoWindowIndex" :clickableIcons="false" :fullscreen-control="false">
            <Circle ref="circleRef" :options="circleParameters" @click="clickCircleEvent"></Circle>
            <InfoWindow ref="circleInfoWindowRef" class="info-window" id="circle-info-window">
                <h3 class="info-window-title">Distance Notification Radius</h3>
                <div id="distance-notifications-radius-input-container">
                    <FloatLabel>
                        <InputNumber id="distance-notifications-radius" class="input" v-model.number="distanceNotificationsRadius" @click="validationErrors.distanceNotificationsRadius = []" :min="1" :max="2000" suffix=" KM"/>
                        <Slider class="input" v-model="distanceNotificationsRadius" :min="1" :max="2000" @click="validationErrors.distanceNotificationsRadius = []"/>
                        <label for="distance-notifications-radius">Distance Notifications Radius (KM)</label>
                    </FloatLabel>
                </div>
                <div class="validation-errors">
                    <TransitionGroup name="errors">
                        <template v-for="distanceNotificationsRadius in validationErrors.distanceNotificationsRadius" :key="distanceNotificationsRadius" class="validation-errors">
                            <div class="validation-error">{{distanceNotificationsRadius}}</div>
                        </template>
                    </TransitionGroup>
                </div>
                <div>
                    <Button label="Save" severity="success" icon="pi pi-check" plain text @click="updateDistanceNotificationsRadius"></Button>
                </div>
            </InfoWindow>
            <CustomControl :position="responsiveMenuDisplayed ? 'LEFT_TOP' : 'TOP_CENTER'">
                <Button class="map-button-top" @click="sideBarVisible = true" icon="pi pi-filter" rounded label="Filters"/>
                <Button class="map-button-top" @click="addressFiltersStore.clearFilters" icon="pi pi-filter-slash" rounded aria-label="Clear filters"/>
                <Button class="map-button-top" @click="addressFiltersStore.fetchAddressesWithFilters" icon="pi pi-refresh" rounded aria-label="Refresh"/>
            </CustomControl>
            <MarkerCluster>
                <Marker v-for="(address, i) in addressStore.addresses.data" @click="openInfoWindow(i)" :options="{position: address.position, icon: {url: address.icon,  scaledSize: { width: 30, height: 30 }}}">
                    <InfoWindow :ref="(el) => (infoWindows[i] = el)" class="info-window">
                        <div>
                            <template v-if="openedInfoWindowIndex !== undefined && openedInfoWindowIndex === i">
                                <template v-if="address.users.length > 0">
                                    <h3 class="info-window-title">Users</h3>
                                    <template v-for="user in address.users">
                                        <router-link class="user-profile-link" :to="{name: 'user-profile', params: {username: user.username}}">

                                            <div class = user-image-container>
                                                <img class="user-image" :src="user.profilePicture.url"  alt="User Image">
                                            </div>
                                            <div class="user-username">
                                                {{ user.username }}
                                            </div>
                                        </router-link>
                                        <div v-if="user.isModder" class="user-is-modder-container">
                                            <Tag value="Modder" rounded :style="{background: 'aqua', marginRight: '5px'}"></Tag>
                                        </div>
                                        <div class="user-games-characters">
                                            <template v-for="game in user.games">
                                                <div class="user-game-tag">
                                                    <Tag :value="game.name" rounded :style="{background: game.color, marginRight: '5px'}"></Tag>
                                                </div>
                                                <template v-for="character in game.characters">
                                                    <img class="user-character-image" :alt="character.name" :src="character.image.url" width="20" />
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </template>
                                <template v-if="address.events.length > 0">
                                    <h3 class="info-window-title">Events</h3>
                                    <template v-for="event in address.events">
                                        <div class="event-image-container">
                                            <img v-if="event.image" :src="event.image.url" alt="Event Image" class="event-image">
                                            <img v-else src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png" alt="Event Image" class="event-image">
                                        </div>
                                        <div class="event-title">
                                            <a :href="event.link" target="_blank"><i class="pi pi-external-link"/> {{ event.title }}</a>
                                        </div>
                                        <div class="event-game-attendees">
                                            <Tag :value="event.game.name" rounded :style="{background: event.game.color, marginRight: '1rem'}"></Tag>
                                            <Chip :label="event.attendees || event.attendees === 0 ? event.attendees.toString() : 'Private'" icon="pi pi-users" :style="{marginRight: '1rem'}"></Chip>
                                            <Button
                                                v-if='userStore.user.data.id'
                                                class="event-bell-button"
                                                @click="eventsStore.handleEventSubscription(event, darkMode)"
                                                :loading="eventsStore.subscriptionLoading"
                                                icon="pi pi-bell"
                                                :class='{ active: event.user_subscribed }'
                                                rounded
                                                aria-label="Notification"
                                            />
                                        </div>
                                        <div class="event-datetime"><Chip :label="event.timezone_start_date_time + ' / ' + event.timezone_end_date_time + ' ' + event.timezone" icon="pi pi-clock"></Chip></div>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </InfoWindow>
                </Marker>
            </MarkerCluster>
            <CustomControl :position="responsiveMenuDisplayed ? 'LEFT_BOTTOM' : 'BOTTOM_CENTER'">
                <Button class="map-button-bottom margin-bottom-15-important" @click="legendsVisible = true" icon="pi pi-filter"  rounded label="Legends"/>
                <Sidebar v-model:visible="legendsVisible" position="bottom" style="height: min-content;">
                    <div id="games-legend">
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #FAC41A;"></div>
                            <div class="game-name">64</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #A30010;"></div>
                            <div class="game-name">Melee</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #660d02;"></div>
                            <div class="game-name">Brawl</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #3B448B;"></div>
                            <div class="game-name">Project M</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #6FD19C;"></div>
                            <div class="game-name">Project +</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #AFC1EE;"></div>
                            <div class="game-name">3DS & Wii U</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: #F18A41;"></div>
                            <div class="game-name">Ultimate</div>
                        </div>
                        <div class="game-legend">
                            <div class="color-box" style="background-color: black;"></div>
                            <div class="game-name">Multiple Games</div>
                        </div>
                    </div>
                </Sidebar>
            </CustomControl>
        </GoogleMap>
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<style>

.dark .gm-style-iw-d{
    color: white;
    scrollbar-color: #494949 #494949 ;
}

.gm-style-iw-d{
    overflow: auto !important;
}

.dark .map-button-top, .dark .map-button-bottom{
    color: white !important;
    background-color: #1E1E1E !important;
}

.map-button-top, .map-button-bottom{
    color: #1E1E1E !important;
    background-color: white !important;
}

.map-button-top {
    margin-top: 15px;
    margin-right: 5px;
    margin-left: 5px;
}

.map-button-bottom{
    margin-bottom: 15px;
}



@media(max-width: 960px){
    .map-button-top, .map-button-bottom{
        margin-left:8px !important;
    }
}

#games-legend{
    display:flex;
    flex-wrap: wrap;
    justify-content: center;
}

.game-legend {
    display: flex;
    align-items: center;
    margin: 10px;
}


.color-box {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.game-name {
    font-size: 16px;
}

.event-bell-button, .event-bell-button:hover, .event-bell-button.active, .event-bell-button:focus, .p-button.event-bell-button:enabled{
    background: transparent;
}

.event-bell-button:hover .p-button-icon,
.event-bell-button.active .p-button-icon{
    color: gold;
}

.event-bell-button.active:hover .p-button-icon,
.event-bell-button .p-button-icon{
    color: grey;
}

.dark .gm-style-iw{
    background-color: #1E1E1E;
}

.dark .gm-style .gm-style-iw-tc::after{
    background: #1E1E1E;
}

.gm-style .gm-style-iw.gm-style-iw-c{
    padding: 12px !important;
}

.dark .poi-info-window div, .dark .poi-info-window a{
    background-color: #1E1E1E;
    color:white
}

.info-window{
    text-align: center;
}

.event-image-container{
    margin-top: 10px;
    margin-bottom: 10px;
}

.event-image{
    height: 80px;
}

.dark .info-window-title,
.dark .user-username{
    color: white;
}

.dark .event-title{
    color: white;
}

.dark .gm-ui-hover-effect > span{
    background-color: white;
}

.event-title>a{
    color: inherit;
    text-decoration: none;
}

.event-title{
    font-size: 1.3em;
}


.event-game-attendees{
    margin-top: 10px;
    margin-bottom: 10px;
}

.user-profile-link{
    text-decoration: none;
    color: inherit;
}

.user-image{
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.user-username{
    margin-top: 5px;
    font-weight: bold;
}

.user-is-modder-container{
    padding-top: 20px
}

.user-games-characters{
    padding: 10px;
}

.user-game-tag{
    margin:10px;
}

.user-character-image {
    margin: 5px;
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

#distance-notifications-radius-input-container{
    width: 300px;
    margin: 10px 20px;
}

.input,
.input{
    width: 100%;
}

.input{
    display: flex;
}



@media(max-width: 480px){
    #circle-info-window {
        width: 280px;
    }
    #distance-notifications-radius-input-container {
        width: 250px;
    }

    #circle-info-window .validation-errors {
        width: 280px;
        min-height: 2.3em
    }
    .gm-style-iw:has(#circle-info-window){
        max-width: none !important;
    }

    .gm-style-iw{
        max-width: 280px !important;
    }
}

/* TODO Query the Marker's image in a batter way */
.gm-style > div > div > div > div > img{
    border-radius: 50%;
}

</style>
