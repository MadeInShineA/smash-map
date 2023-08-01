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

</script>

<template>
    <h1>Map</h1>
    <Button class="filters-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined plain label="Filters"/>
    <FilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></FilterSidebar>
</template>

<style scoped>

</style>
