<script setup>

import Sidebar from "primevue/sidebar";
import Calendar from "primevue/calendar";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import { useEventFiltersStore } from '../stores/EventFiltersStore.js'
import {useOptionsStore} from "../stores/OptionsStore.js";
import {useUserStore} from "../stores/UserStore.js";

const filtersStore = useEventFiltersStore()
const optionsStore = useOptionsStore()
const userStore = useUserStore()

filtersStore.fetchCountries({params: {continents: filtersStore.selectedEventContinents.length > 0 ? filtersStore.selectedEventContinents.join(',') : 'default'}})
filtersStore.fetchEventsWithFilters()

const props = defineProps({
    sideBarVisible: {
        type: Boolean,
        required: true
    }
})

const emit = defineEmits(['switchSideBarVisible'])

</script>

<template>
    <Sidebar :visible="sideBarVisible" @update:visible="emit('switchSideBarVisible')" position="top" style="text-align: center; height: min-content; max-height:100vh">
        <h2>Filters</h2>
        <div id="event-filters">
            <div class="event-filter">
                <MultiSelect v-model="filtersStore.selectedEventGames" :options="optionsStore.gameOptions" display="chip" :maxSelectedLabels="2" optionLabel="name" optionValue="id" placeholder="Select Games"/>
            </div>
            <div class="event-filter">
                <Dropdown v-model="filtersStore.selectedEventTypes" :options="userStore.user.data.id ? optionsStore.connectedUserEventTypeOptions : optionsStore.eventTypeOptions" optionLabel="name" optionValue="value" placeholder="All event types"/>
            </div>
            <div class="event-filter">
                <Calendar v-model=filtersStore.selectedEventDates :minDate="new Date()" placeholder="Event date range (UTC)" selectionMode="range" :manualInput="false" showButtonBar dateFormat="dd/mm/yy"></Calendar>
            </div>
            <div class="event-filter">
                <MultiSelect v-model="filtersStore.selectedEventContinents" :options="optionsStore.continentOptions" display="chip" :disabled="filtersStore.selectedEventTypes === 'online'" :maxSelectedLabels="2" optionLabel="name" optionValue="code" placeholder="Select Continents"/>
            </div>
            <div class="event-filter">
                <!--            TODO Directly add the data to eventCountryOptions-->
                <MultiSelect :loading="!filtersStore.countriesFetched" v-model="filtersStore.selectedEventCountries" :options="filtersStore.eventCountryOptions.data" filter display="chip" :disabled="filtersStore.selectedEventTypes === 'online' || !filtersStore.countriesFetched" :maxSelectedLabels="2" optionLabel="name" optionValue="code" placeholder="Select Countries">
                    <template #option="slotProps">
                        <div class="country-flag">
                            <img v-if="slotProps.option.image" :alt="slotProps.option.name + '-image'" :src="slotProps.option.image.url" class="country-flag-image" />
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>
            <div class="event-filter">
                <InputText v-model="filtersStore.selectedEventName" placeholder="Event name"></InputText>
            </div>
        </div>
    </Sidebar>
</template>

<style scoped>

#event-filters{
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.event-filter{
    margin:20px 10px;
}

.country-flag{
    display: flex;
}

.country-flag-image{
    width: 18px;
    margin-right: 5px;
}

</style>
