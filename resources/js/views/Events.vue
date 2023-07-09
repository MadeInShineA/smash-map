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

const orderByOptions = ref([
    {name: 'Sort by ID', value: 'default'},
    {name: 'Attendees ascending', value: 'attendeesASC'},
    {name: 'Attendees descending', value: 'attendeesDESC'},
    {name: 'Date ascending', value: 'dateASC'},
    {name: 'Date descending', value: 'dateDESC'}
])

const eventGameOptions = ref([
    {name: '64', value: '4'},
    {name: 'Melee', value: '1'},
    {name: 'Brawl', value: '5'},
    {name: 'Project M', value: '2'},
    {name: 'Project +', value: '33602'},
    {name: '3DS / WiiU', value: '3'},
    {name: 'Ultimate', value: '1386'},
])

const eventTypeOptions = ref([
    {name: 'All event types', value: 'default'},
    {name: 'Online', value: 'online'},
    {name: 'Offline', value: 'offline'}
]);

const eventContinentOptions = ref( [
    { name: 'Africa', code: 'AF' },
    { name: 'Antarctica', code: 'AN' },
    { name: 'Asia', code: 'AS' },
    { name: 'Europe', code: 'EU' },
    { name: 'North America', code: 'NA' },
    { name: 'Oceania', code: 'OC' },
    { name: 'South America', code: 'SA' }
])

const currentPage = ref(1);
const selectedOrderBy = ref({name: 'Default sort', value: 'default'});
const selectedEventGames = ref([]);
const selectedEventType = ref({name: 'Default type', value: 'default'});
const selectedEventDates = ref([])
const selectedEventContinents = ref([]);
const selectedEventCountries = ref([]);
const selectedEventName = ref('')


import { useAxios } from '@vueuse/integrations/useAxios'
import {useDateFormat, watchDebounced, watchPausable} from '@vueuse/core'
import { watch } from 'vue'

const { data: events, isFinished: eventsFetched, execute: fetchEvents } = useAxios('/api/events')

const {data: eventCountryOptions, isFinished: countriesFetched, execute: fetchCountries} = useAxios('/api/countries-filter')

const {pause: pauseCurrentPageWatch, resume: resumeCurrentPageWatch } = watchPausable([currentPage], function([page]){
    let startDate
    let endDate

    if(selectedEventDates.value){
        startDate = selectedEventDates.value[0]
        if (startDate){
            startDate = useDateFormat(startDate,'YYYY-MM-DD')
            startDate = startDate.value
        }else{
            startDate = 'default'
        }

        endDate = selectedEventDates.value[1]
        if (endDate){
            endDate = useDateFormat(endDate,'YYYY-MM-DD')
            endDate = endDate.value
        }else{
            endDate = startDate
        }
    }else{
        startDate = 'default'
        endDate = 'default'
    }

    const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.map(obj => obj.value).join(',') : 'default'
    const type = selectedEventType.value.value
    const orderBy = selectedOrderBy.value.value
    const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
    const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
    const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

    fetchEvents({ params: { page, games, type, orderBy, continents, countries, name, startDate, endDate}})
})


const {pause: pauseContinentsWatch, resume: resumeContinentsWatch } = watchPausable([selectedEventContinents], function([continents]){
    continents = continents.length > 0 ? continents.map(continent => continent.code).join(',') : 'default'
    fetchCountries({params: {continents}})

    let startDate
    let endDate
    if(selectedEventDates.value){
        startDate = selectedEventDates.value[0]
        if (startDate){
            startDate = useDateFormat(startDate,'YYYY-MM-DD')
            startDate = startDate.value
        }else{
            startDate = 'default'
        }

        endDate = selectedEventDates.value[1]
        if (endDate){
            endDate = useDateFormat(endDate,'YYYY-MM-DD')
            endDate = endDate.value
        }else{
            endDate = startDate
        }
    }else{
        startDate = 'default'
        endDate = 'default'
    }

    pauseCurrentPageWatch()
    currentPage.value = 1
    resumeCurrentPageWatch()
    const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.map(obj => obj.value).join(',') : 'default'
    const type = selectedEventType.value.value
    const orderBy = selectedOrderBy.value.value
    const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
    const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

    fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})


}, {immediate: false})

const {pause: pauseCountriesWatch, resume: resumeCountriesWatch} = watchPausable([selectedEventCountries], function([countries]){
    countries = countries.length > 0 ? countries.map(country => country.code).join(',') : 'default'

    let startDate
    let endDate
    if(selectedEventDates.value){
        startDate = selectedEventDates.value[0]
        if (startDate){
            startDate = useDateFormat(startDate,'YYYY-MM-DD')
            startDate = startDate.value
        }else{
            startDate = 'default'
        }

        endDate = selectedEventDates.value[1]
        if (endDate){
            endDate = useDateFormat(endDate,'YYYY-MM-DD')
            endDate = endDate.value
        }else{
            endDate = startDate
        }
    }else{
        startDate = 'default'
        endDate = 'default'
    }

    pauseCurrentPageWatch()
    currentPage.value = 1
    resumeCurrentPageWatch()

    const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.map(obj => obj.value).join(',') : 'default'
    const type = selectedEventType.value.value
    const orderBy = selectedOrderBy.value.value
    const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
    const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

    fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})
}, {immediate: false})


watch([selectedOrderBy, selectedEventGames, selectedEventType, selectedEventDates], function([{value: orderBy} , games, {value: type}, dates]){
    if (type === 'online'){

        pauseContinentsWatch()
        selectedEventContinents.value = []
        resumeContinentsWatch()

        eventCountryOptions.value = []
    }

    let startDate
    let endDate
    if(selectedEventDates.value){
        startDate = dates[0]
        if (startDate){
            startDate = useDateFormat(startDate,'YYYY-MM-DD')
            startDate = startDate.value
        }else{
            startDate = 'default'
        }

        endDate = dates[1]
        if (endDate){
            endDate = useDateFormat(endDate,'YYYY-MM-DD')
            endDate = endDate.value
        }else{
            endDate = startDate
        }
    }else{
        startDate = 'default'
        endDate = 'default'
    }

    pauseCurrentPageWatch()
    currentPage.value = 1
    resumeCurrentPageWatch()

    games = games.length > 0 ? games.map(obj => obj.value).join(',') : 'default'
    const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
    const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
    const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

    fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})
}, {immediate: false})

watch(eventCountryOptions, function(availableCountries, oldValue){
    //TODO Directly add the data to availableCountries
    if (oldValue){
        const availableCountryCodes = availableCountries.data.map(country => country.code)
        pauseCountriesWatch()
        selectedEventCountries.value = selectedEventCountries.value.filter(country => availableCountryCodes.includes(country.code))
        resumeCountriesWatch()
    }
}, {immediate: false})

watchDebounced([selectedEventName], function([name]){
    name = name !== '' ? name : 'default'

    let startDate
    let endDate
    if(selectedEventDates.value){
        startDate = selectedEventDates.value[0]
        if (startDate){
            startDate = useDateFormat(startDate,'YYYY-MM-DD')
            startDate = startDate.value
        }else{
            startDate = 'default'
        }

        endDate = selectedEventDates.value[1]
        if (endDate){
            endDate = useDateFormat(endDate,'YYYY-MM-DD')
            endDate = endDate.value
        }else{
            endDate = startDate
        }
    }else{
        startDate = 'default'
        endDate = 'default'
    }

    const page = currentPage.value
    const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.map(obj => obj.value).join(',') : 'default'
    const type = selectedEventType.value.value
    const orderBy = selectedOrderBy.value.value
    const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
    const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
    fetchEvents({ params: { page, games, type, orderBy, continents, countries, name, startDate, endDate}})

}, { immediate: false, debounce: 600, maxWait: 1000 })

const sideBarVisible = ref(false)

onMounted(()=>{
    console.log('Events Mounted')
})

</script>

<template>
    <div class="event-filters">
        <div class="event-filter">
            <Dropdown v-model="selectedOrderBy" :options="orderByOptions" optionLabel="name" placeholder="Sort by ID"/>
        </div>
        <Button class="filters-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined plain label="Filters"/>
    </div>
    <Sidebar v-model:visible="sideBarVisible" position="top" id="event-filters-sidebar">
        <h2>Filters</h2>
        <div class="event-filters">
            <div class="event-filter">
                <MultiSelect v-model="selectedEventGames" :options="eventGameOptions" display="chip" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Games"/>
            </div>
            <div class="event-filter">
                <Dropdown v-model="selectedEventType" :options="eventTypeOptions" optionLabel="name" placeholder="All event types"/>
            </div>
            <div class="event-filter">
                <Calendar v-model=selectedEventDates :minDate="new Date()" placeholder="Event date range (UTC)" selectionMode="range" :manualInput="false" showButtonBar dateFormat="dd/mm/yy"></Calendar>
            </div>
            <div class="event-filter">
                <MultiSelect v-model="selectedEventContinents" :options="eventContinentOptions" display="chip" :disabled="selectedEventType.value === 'online'" :maxSelectedLabels="2" optionLabel="name" placeholder="Select Continents"/>
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
    </Sidebar>
    <template v-if="countriesFetched && eventsFetched">
        <template v-if="events.data.length > 0">
            <div id="event-container">
                <Card v-for="event in events.data" :key="event.id" class="event-card">
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
