<script setup>

import {onMounted, ref} from "vue";

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

onMounted(()=>{
    console.log('Events Mounted')
})


const orderByOptions = ref([
    {name: 'Sort by ID', value: 'default'},
    {name: 'Attendees ascending', value: 'attendeesASC'},
    {name: 'Attendees descending', value: 'attendeesDESC'},
    {name: 'Date ascending', value: 'dateASC'},
    {name: 'Date descending', value: 'dateDESC'}
])

const eventTypeOptions = ref([
    {name: 'All event types', value: 'default'},
    {name: 'Online', value: 'online'},
    {name: 'Offline', value: 'offline'}
]);

const eventContinentOptions = ref( [
    { name: 'Africa', value: 'AF' },
    { name: 'Antarctica', value: 'AN' },
    { name: 'Asia', value: 'AS' },
    { name: 'Europe', value: 'EU' },
    { name: 'North America', value: 'NA' },
    { name: 'Oceania', value: 'OC' },
    { name: 'South America', value: 'SA' }
])

// TODO Correct the double load request + loading disappearing when the debounce is higher
const currentPage = ref(1);
const selectedOrderBy = ref({name: 'Default sort', value: 'default'});
const selectedEventType = ref({name: 'Default type', value: 'default'});
const selectedEventContinents = ref([]);
const selectedEventCountries = ref([]);
const loading=ref(true)
import { useAxios } from '@vueuse/integrations/useAxios'
import { watchDebounced } from '@vueuse/core'
import { watch } from 'vue'
const selectedEventName = ref('')

watch(selectedEventType, function(type){
    if (type.value !== 'online') return
    selectedEventCountries.value = []
    selectedEventContinents.value = []
}, { immediate: true })

const {data: eventCountryOptions, isFinished: countriesFetched, execute: fetchCountries} = useAxios('/api/countries-filter')
watch(selectedEventContinents, function(continents){
    continents = continents.length > 0 ? continents.map(obj => obj.value).join(',') : 'default'
    fetchCountries(
        {
            params: {continents},

            //TODO Directly add the data to eventCountryOptions
            // transformResponse: function(response){
            //     return response['data']
            // },

        }
    )
} ,{immediate: true})

watch(eventCountryOptions, function(availableCountries){
    //TODO Directly add the data to availableCountries
    selectedEventCountries.value = selectedEventCountries.value.filter(country => availableCountries.data.includes(country))
})

const { data: events, isFinished: eventsFetched, execute: fetchEvents } = useAxios('/api/events')
watchDebounced([selectedEventName, selectedEventType, selectedOrderBy, selectedEventContinents, selectedEventCountries], function([name, { value: type }, { value: orderBy }, continents, countries]){
    continents = continents.length > 0 ? continents.map(obj => obj.value).join(',') : 'default'
    countries = countries.length > 0 ? countries.map(obj => obj.value).join(',') : 'default'
    fetchEvents({ params: { page: 1, type, orderBy, continents, countries, name }})
}, { immediate: true, debounce: 200, maxWait: 1000 })


watch(currentPage, function (page){
    const type = selectedEventType.value.value
    const orderBy = selectedOrderBy.value.value
    const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(obj => obj.value).join(',') : 'default'
    const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(obj => obj.value).join(',') : 'default'
    const name = selectedEventName.value

    fetchEvents({ params: { page, type, orderBy, continents, countries, name }})
})

</script>

<template>
    <div id="event-filters">
        <div class="event-filter">
            <Dropdown v-model="selectedOrderBy" :options="orderByOptions" optionLabel="name" placeholder="Sort by ID"/>
        </div>
        <div class="event-filter">
            <Dropdown v-model="selectedEventType" :options="eventTypeOptions" optionLabel="name" placeholder="All event types"/>
        </div>

        <div class="event-filter">
            <MultiSelect v-model="selectedEventContinents" :options="eventContinentOptions" filter display="chip" :disabled="selectedEventType.value === 'online'" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Continents"/>
        </div>
        <div class="event-filter">
<!--            TODO Directly add the data to eventCountryOptions-->
            <MultiSelect v-if="countriesFetched" v-model="selectedEventCountries" :options="eventCountryOptions.data" filter display="chip" :disabled="selectedEventType.value === 'online'" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Countries">
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
                <InputText v-model="selectedEventName" placeholder="Event name"></InputText>
            </span>
        </div>
    </div>
    <template v-if="countriesFetched && eventsFetched">
        <template v-if="events.data.length > 0">
            <div class="event-container">
                <Card v-for="event in events.data" :key="event.id" class="event-card">
                    <template #header>
                        <div class="event-image-container">
                            <img v-if="event.images[0]" :src="event.images[0].url" alt="Event Image" class="event-image" @error="$event.target.src = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png'">
                            <img v-else src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png" alt="Event Image" class="event-image">
                        </div>
                    </template>
                    <template #title>
                        <a class="event-title" :href=event.link target="_blank"><i class="pi pi-external-link"/> {{ event.name }}</a>
                    </template>
                    <template #content>
                        <div class="event-attendees"><Chip :label="event.attendees || event.attendees === 0 ? event.attendees.toString() : 'Private'" icon="pi pi-users"></Chip></div>
                        <div v-if="!event.is_online" class="event-location"><Chip :label="event.address.name" icon="pi pi-map-marker"></Chip></div>
                        <div v-else class="event-location"><Chip label="Online" icon="pi pi-globe"></Chip></div>
                        <div class="event-datetime"><Chip :label="event.timezone_start_date_time + ' / ' + event.timezone_end_date_time + ' ' + event.timezone" icon="pi pi-clock"></Chip></div>
                    </template>
                </Card>
            </div>
            <Paginator  v-if="events.meta.total > events.meta.per_page" :first="currentPage * (events.meta.per_page) -1" :rows="events.meta.per_page" :total-records="events.meta.total" @page="currentPage = $event.page + 1"/>
        </template>
        <template v-else>
            <h1 id="no-events">No events correspond to your filters</h1>
        </template>
    </template>
    <template v-if="!countriesFetched || !eventsFetched">
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<!-- TODO Double check the style and not forget responsive -->
<style>

/*TODO responsive filters style */
#event-filters{
    display:flex;
    justify-content: center;
    flex-wrap: wrap;
}

.event-filter{
    margin:20px;
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

.event-container {
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

.event-attendees{
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
    .event-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 850px) {
    .event-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    .event-container {
        grid-template-columns: 1fr;
    }
}

</style>
