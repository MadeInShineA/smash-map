<script setup>

import {onMounted, ref} from "vue";
import FilterSidebar from "@/components/FilterSidebar.vue";
import Button from "primevue/button";
// import { GoogleMap, Marker } from "vue3-google-map";

    onMounted(()=>{
        console.log('Map Mounted')
    })

const sideBarVisible = ref(false)
const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}

const center = ref([40, 40]);
const projection = ref("EPSG:4326");
const zoom = ref(8);
const rotation = ref(0);

</script>

<template>
    <Button class="filters-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined plain label="Filters"/>
    <FilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></FilterSidebar>

    <ol-map
        :loadTilesWhileAnimating="true"
        :loadTilesWhileInteracting="true"
        style="height: 90vh"
    >
        <ol-view
            ref="view"
            :center="center"
            :rotation="rotation"
            :zoom="zoom"
            :projection="projection"
        />

        <ol-tile-layer>
            <ol-source-osm />
        </ol-tile-layer>
    </ol-map>

</template>

<style scoped>

</style>
