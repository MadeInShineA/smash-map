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
const sideBarVisible = ref(false)
const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}

const center = ref({ lat: 40.689247, lng: -74.044502 });
const infoWindows = ref([]);

const closeInfoWindows = (i) => { infoWindows.value.forEach((ref, index) => { if (index !== i) { ref.infoWindow.close(); } }); };

const clickMarkerEvent = (i) => { closeInfoWindows(i); };

const { data: addresses, isFinished: addressesFetched, execute: fetchAddresses } = useAxios('/api/addresses')

onMounted(()=>{
    console.log('Map Mounted')
})

let googleMapApiKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;

</script>

<template>
    <template v-if="addressesFetched">
<!--        <div id="event-filters-container">-->
<!--            <Button class="filters-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined label="Filters"/>-->
<!--            <FilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></FilterSidebar>-->
<!--        </div>-->
        <GoogleMap :api-key="googleMapApiKey" style="width: 100%; height: 100%" :center="center" :zoom="15"  @click="closeInfoWindows">
            <MarkerCluster>
                <Marker v-for="(address, i) in addresses.data" @click="clickMarkerEvent" :options="{position: address.position, icon: {url: address.icon,  scaledSize: { width: 20, height: 20 }}}">
                    <InfoWindow :ref="(el) => (infoWindows[i] = el)">
                        <div v-for="event in address.events">
                            <h1>
                                {{event.title}}
                            </h1>
                        </div>
                    </InfoWindow>
                </Marker>
            </MarkerCluster>
            <CustomControl position="BOTTOM_CENTER">
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
                </div>
            </CustomControl>
        </GoogleMap>
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<style scoped>

#event-filters-container{
    position: absolute;
    width: 100vw;
    z-index: 1;
    text-align: center;
    margin-top: 10px;
}

.filters-button{
    color: black;
}

#games-legend{
    display:flex;
    background-color: white;
}
.dark #games-legend{
    background-color:#1E1E1E;
}
.game-legend {
    display: flex;
    align-items: center;
    margin: 10px;
}

.dark .game-legend{
    color: white;
}

.color-box {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.game-name {
    font-size: 16px;
}
</style>
