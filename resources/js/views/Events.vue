<script setup>

import {onMounted, ref} from "vue";
import { useAxios } from '@vueuse/integrations/useAxios'
import {useDateFormat, watchDebounced, watchPausable} from '@vueuse/core'
import { watch } from 'vue'
import Dropdown from "primevue/dropdown";
import Button from "primevue/button";
import Sidebar from "primevue/sidebar";
import MultiSelect from "primevue/multiselect";
import Calendar from "primevue/calendar";
import InputText from "primevue/inputtext"
import Card from "primevue/card";
import Paginator from "primevue/paginator";
import Chip from "primevue/chip";
import Tag from "primevue/tag";
import {useFiltersStore} from "../stores/FiltersStore.js";
import {useEventsStore} from "../stores/EventsStore.js";
import FilterSidebar from "@/components/FilterSidebar.vue";

const filtersStore = useFiltersStore()
const eventsStore = useEventsStore()

const sideBarVisible = ref(false)
const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}
// TODO Update the timezone based on the user's IP

// const timezone = ref('UTC')

// const getTimezone = async function(){
//     try{
//         const response = await axios.get('/api/timezone')
//         timezone.value=response.data
//     } catch(error){
//         timezone.value = 'UTC'
//     }
//
// }
//
// getTimezone()

const orderByOptions = ref([
    {name: 'Sort by ID', value: 'default'},
    {name: 'Attendees ascending', value: 'attendeesASC'},
    {name: 'Attendees descending', value: 'attendeesDESC'},
    {name: 'Date ascending', value: 'dateASC'},
    {name: 'Date descending', value: 'dateDESC'}
])

onMounted(()=>{
    console.log('Events Mounted')
})

</script>

<template>
    <div class="event-filters">
        <div class="event-filter">
            <Dropdown v-model="filtersStore.selectedOrderBy" :options="orderByOptions" optionLabel="name" placeholder="Sort by ID"/>
        </div>
        <Button class="filters-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined plain label="Filters"/>
    </div>
    <FilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></FilterSidebar>
    <template v-if="filtersStore.countriesFetched && eventsStore.eventsFetched">
        <template v-if="eventsStore.events.data.length > 0">
            <div id="event-container">
                <Card v-for="event in eventsStore.events.data" :key="event.id" class="event-card">
                    <template #header>
                        <div class="event-image-container">
                            <img v-if="event.images[0]" :src="event.images[0].url" alt="Event Image" class="event-image">
                            <img v-else src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png" alt="Event Image" class="event-image">
                        </div>
                    </template>
                    <template #title>
                        <a class="event-title" :href=event.link target="_blank"><i class="pi pi-external-link"/> {{ event.name }}</a>
                    </template>
                    <template #content>
                        <div class="event-game-attendees"><Tag :value="event.game.name" rounded :style="{background: event.game.color, marginRight: '5px'}"></Tag><Chip :label="event.attendees || event.attendees === 0 ? event.attendees.toString() : 'Private'" icon="pi pi-users"></Chip></div>
                        <div v-if="!event.is_online" class="event-location"><Chip :label="event.address.name" icon="pi pi-map-marker"></Chip></div>
                        <div v-else class="event-location"><Chip label="Online" icon="pi pi-globe"></Chip></div>
                        <div class="event-datetime"><Chip :label="event.timezone_start_date_time + ' / ' + event.timezone_end_date_time + ' ' + event.timezone" icon="pi pi-clock"></Chip></div>
                    </template>
                </Card>
            </div>
            <Paginator  v-if="eventsStore.events.meta.total > eventsStore.events.meta.per_page" :first="filtersStore.currentPage * (eventsStore.events.meta.per_page) -1" :rows="eventsStore.events.meta.per_page" :total-records="eventsStore.events.meta.total" @page="filtersStore.currentPage = $event.page + 1"/>
        </template>
        <template v-else>
            <h1 id="no-events">No events correspond to your filters</h1>
        </template>
    </template>
    <template v-if="!filtersStore.countriesFetched || !eventsStore.eventsFetched">
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<style>

.event-filters{
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.filters-button{
    height: min-content;
    align-self: center;
}

#event-filters-sidebar{
    text-align: center;
    height: min-content;
}

.event-filter{
    margin:20px 10px;
}

/* Hide the No selected item text on the dropdowns */

.p-hidden-accessible{
    display:none;
}

.country-flag{
    display: flex;
}

.country-flag-image{
    width: 18px;
    margin-right: 5px;
}

#event-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    justify-items: center;
    grid-gap: 20px;
    margin-left: 20px;
    margin-right: 20px;
}


.event-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    text-align: center;
    margin-top:15px;
    margin-bottom: 15px;
}

.event-image-container {
    display: flex;
    justify-content: center;
    margin-top:20px;
    margin-bottom: 10px;
}

.event-image {
    max-width: 100%;
    height: 200px;
}

.p-card-title{
    height: 2em;
}

/*TODO Fix the ellipsis */
.event-title {
    margin-bottom: 10px;
    text-decoration: none;
    color: inherit;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}


.event-location {
    margin-bottom: 10px;
    height: 5em;
}

.event-location .p-chip-text{
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.event-game-attendees{
    margin-bottom: 10px;
    height: 2em;
}

.event-datetime {
    margin-bottom: 0;
    height: 5em;
}

.p-paginator{
   background: none;
}

#no-events{
    text-align:center;
}

@media (max-width: 1100px) {
    #event-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 850px) {
    #event-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    #event-container {
        grid-template-columns: 1fr;
    }
}

</style>
