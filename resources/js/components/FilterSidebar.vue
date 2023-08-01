<script setup>

import Sidebar from "primevue/sidebar";
import Calendar from "primevue/calendar";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import { useFiltersStore } from '../stores/FiltersStore.js'

const filtersStore = useFiltersStore()

const props = defineProps({
    sideBarVisible:Boolean
})

const emit = defineEmits(['switchSideBarVisible'])

</script>

<template>
    <Sidebar :visible="sideBarVisible" @update:visible="emit('switchSideBarVisible')" position="top" id="event-filters-sidebar">
        <h2>Filters</h2>
        <div class="event-filters">
            <div class="event-filter">
                <MultiSelect v-model="filtersStore.selectedEventGames" :options="filtersStore.eventGameOptions" display="chip" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Games"/>
            </div>
            <div class="event-filter">
                <Dropdown v-model="filtersStore.selectedEventTypes" :options="filtersStore.eventTypeOptions" optionLabel="name" placeholder="All event types"/>
            </div>
            <div class="event-filter">
                <Calendar v-model=filtersStore.selectedEventDates :minDate="new Date()" placeholder="Event date range (UTC)" selectionMode="range" :manualInput="false" showButtonBar dateFormat="dd/mm/yy"></Calendar>
            </div>
            <div class="event-filter">
                <MultiSelect v-model="filtersStore.selectedEventContinents" :options="filtersStore.eventContinentOptions" display="chip" :disabled="filtersStore.selectedEventTypes.value === 'online'" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Continents"/>
            </div>
            <div class="event-filter">
                <!--            TODO Directly add the data to eventCountryOptions-->
                <MultiSelect v-if="filtersStore.countriesFetched" v-model="filtersStore.selectedEventCountries" :options="filtersStore.eventCountryOptions.data" filter display="chip" :disabled="filtersStore.selectedEventTypes.value === 'online'" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Countries">
                    <template #option="slotProps">
                        <div class="country-flag">
                            <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="country-flag-image" />
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
                <MultiSelect v-else disabled loading filter placeholder="Select Countries"/>
            </div>
            <div class="event-filter">
        <span class="p-input-icon-left">
            <i class="pi pi-search"/>
            <InputText v-model="filtersStore.selectedEventName" placeholder="Event name"></InputText>
        </span>
            </div>
        </div>
    </Sidebar>
</template>

<style scoped>

</style>
