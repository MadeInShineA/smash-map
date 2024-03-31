<script>
export default { name:'map'}
</script>

<script setup>
import {GoogleMap, CustomControl, Marker, MarkerCluster, InfoWindow} from "vue3-google-map";
import {onMounted, ref, watch} from "vue";
import AddressFilterSidebar from "@/components/AddressFilterSidebar.vue";
import Button from "primevue/button";
import LoaderComponent from "@/components/LoaderComponent.vue";
import Sidebar from "primevue/sidebar";
import Chip from "primevue/chip";
import Tag from "primevue/tag";
import { useAddressesStore } from '../stores/AddressesStore.js'
import { useEventsStore } from "../stores/EventsStore.js";
import { useUserStore } from "../stores/UserStore.js";

const props = defineProps({
    // user: Object,
    responsiveMenuDisplayed: Boolean
})

const addressStore = useAddressesStore()

addressStore.fetchAddresses()

const eventsStore = useEventsStore()

const userStore = useUserStore()

const sideBarVisible = ref(false)

const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}

const center = ref({ lat: 40.713956, lng: -38.716136 });
const zoom = ref(4);
const infoWindows = ref([]);
const mapRef = ref();

//TODO Understand why this watch works but not this one
// watch(addressStore.addressesFetched, function([addresses, oldAddresses]){
//     console.log('Addresses changed')
// })



const closeInfoWindows = (i) => { infoWindows.value.forEach((ref, index) => { if (index !== i) { ref.infoWindow.close(); } }); };



//TODO Fix the Zooming on marker click


function clickMarkerEvent(i) {
    // console.log(zoom.value)
    // if(zoom.value < 6){
    //     console.log('Zooming')
    //     zoom.value = 8;
    closeInfoWindows(i);

}

const legendsVisible = ref(false)

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
    });
})

const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;

</script>

<template>
    <AddressFilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></AddressFilterSidebar>
    <template v-if="addressStore.addressesFetched">
        <GoogleMap ref="mapRef" :api-key="googleMapApiKey" language="en" :map-type-control-options="{ mapTypeIds: ['roadmap','satellite',]}" style="width: 100%; height: 100%" :center="center" :zoom="zoom" :min-zoom="4" @click="closeInfoWindows" :clickableIcons="false" :fullscreen-control="false">
            <CustomControl :position="responsiveMenuDisplayed ? 'LEFT_TOP' : 'TOP_CENTER'">
                <Button class="map-button margin-top-15-important" @click="sideBarVisible = true" icon="pi pi-filter" rounded label="Filters"/>
            </CustomControl>
            <MarkerCluster>
                <Marker v-for="(address, i) in addressStore.addresses.data" @click="clickMarkerEvent" :options="{position: address.position, icon: {url: address.icon,  scaledSize: { width: 30, height: 30 }}}">
                    <InfoWindow :ref="(el) => (infoWindows[i] = el)" class="info-window">
                        <template v-if="address.users.length > 0">
                            <h3 class="category-holder">Users</h3>
                            <template v-for="user in address.users">
                                <div class = user-image-container>
                                    <img :src="user.profilePicture"  alt="User Image">
                                </div>
                                <div class="user-username">
                                    {{ user.username }}
                                </div>
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
                            <h3 class="category-holder">Events</h3>
                            <template v-for="event in address.events">
                                <div class="event-image-container">
                                    <img v-if="event.images[0]" :src="event.images[0].url" alt="Event Image" class="event-image">
                                    <img v-else src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png" alt="Event Image" class="event-image">
                                </div>
                                <div class="event-title">
                                    <a :href="event.link" target="_blank"><i class="pi pi-external-link"/> {{ event.title }}</a>
                                </div>
                                <div class="event-game-attendees">
                                    <Tag :value="event.game.name" rounded :style="{background: event.game.color, marginRight: '5px'}"></Tag>
                                    <Chip :label="event.attendees || event.attendees === 0 ? event.attendees.toString() : 'Private'" icon="pi pi-users"></Chip>
                                    <Button
                                        v-if='userStore.user'
                                        class="event-bell-button"
                                        @click="eventsStore.handleEventSubscription(event)"
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
                    </InfoWindow>
                </Marker>
            </MarkerCluster>
            <CustomControl :position="responsiveMenuDisplayed ? 'LEFT_BOTTOM' : 'BOTTOM_CENTER'">
                <Button class="map-button margin-bottom-15-important" @click="legendsVisible = true" icon="pi pi-filter"  rounded label="Legends"/>
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
    color: black;
}

.dark .map-button{
    color: white !important;
    background-color: #1E1E1E !important;
}

.map-button{
    color: #1E1E1E !important;
    background-color: white !important;

}

@media(max-width: 960px){
    .map-button{
        margin-left:8px !important;
    }
}

.margin-top-15-important{
    margin-top:15px !important;
}

.margin-bottom-15-important{
    margin-bottom:15px !important;
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

.dark .category-holder,
.dark .user-username{
    color: white;
}

.dark .event-title{
    color: white;
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

.user-username{
    margin-top: 5px
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





</style>
