<script>
export default { name:'map'}
</script>

<script setup>
import {GoogleMap, CustomControl, Marker, MarkerCluster, InfoWindow} from "vue3-google-map";
import {onMounted, ref} from "vue";
import FilterSidebar from "@/components/FilterSidebar.vue";
import Button from "primevue/button";
import {useAxios} from "@vueuse/integrations/useAxios";
import LoaderComponent from "@/components/LoaderComponent.vue";
import Sidebar from "primevue/sidebar";
import Chip from "primevue/chip";
import Tag from "primevue/tag";

const props = defineProps({
    user: Object,
    responsiveMenuDisplayed: Boolean
})


const sideBarVisible = ref(false)

const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}

const center = ref({ lat: 40.713956, lng: -38.716136 });
const zoom = ref(4);
const infoWindows = ref([]);

const closeInfoWindows = (i) => { infoWindows.value.forEach((ref, index) => { if (index !== i) { ref.infoWindow.close(); } }); };

const clickMarkerEvent = (i) => { closeInfoWindows(i); };

const { data: addresses, isFinished: addressesFetched, execute: fetchAddresses } = useAxios('/api/addresses')

const legendsVisible = ref(false)

onMounted(()=>{
    console.log('Map Mounted')
    if(navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
       center.value = {lat: position.coords.latitude, lng: position.coords.longitude};
       zoom.value = 10;
      })
    };
})

const googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;

</script>

<template>
    <template v-if="addressesFetched">
        <GoogleMap :api-key="googleMapApiKey" style="width: 100%; height: 100%" :center="center" :zoom="zoom" :min-zoom="4" @click="closeInfoWindows">
            <CustomControl :position="responsiveMenuDisplayed ? 'LEFT_TOP' : 'TOP_CENTER'">
                <Button class="map-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined label="Filters"/>
            </CustomControl>
            <FilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></FilterSidebar>
            <MarkerCluster>
                <Marker v-for="(address, i) in addresses.data" @click="clickMarkerEvent" :options="{position: address.position, icon: {url: address.icon,  scaledSize: { width: 20, height: 20 }}}">
<!--                <Marker v-for="(address, i) in addresses.data" @click="clickMarkerEvent" :options="{position: address.position, icon: {url: 'https://w7.pngwing.com/pngs/107/759/png-transparent-circle-white-circle-white-monochrome-black-thumbnail.png',  scaledSize: { width: 20, height: 20 }}}">-->
                    <InfoWindow :ref="(el) => (infoWindows[i] = el)" class="info-window">
                        <template v-if="address.players">
                            <h3 class="category-holder">Players</h3>
                        </template>
                        <template v-if="address.events">
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
                                </div>
                                <div class="event-datetime"><Chip :label="event.timezone_start_date_time + ' / ' + event.timezone_end_date_time + ' ' + event.timezone" icon="pi pi-clock"></Chip></div>
                            </template>
                        </template>
                    </InfoWindow>
                </Marker>
            </MarkerCluster>
            <CustomControl :position="responsiveMenuDisplayed ? 'LEFT_BOTTOM' : 'BOTTOM_CENTER'">
                <Button class="map-button" @click="legendsVisible = true" icon="pi pi-filter"  rounded outlined plain label="Legends"/>
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

/*
    TODO Add :deep
 */
.dark .gm-style-iw-d{
    color: black;
}

.map-button{
    color: white !important;
}

.dark .map-button{
    color: #1E1E1E !important;
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

.dark .gm-style-iw{
    background-color: #1E1E1E;
}

.dark .gm-style .gm-style-iw-tc::after{
    background: #1E1E1E;
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

.dark .category-holder{
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



</style>
