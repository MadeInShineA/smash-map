<script setup>

import {onMounted, ref} from "vue";

const loading = ref(true)

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

const currentPage = ref(1);

onMounted(()=>{
    console.log('Events Mounted')
})

const selectedOrdering = ref();

const orderByOptions = ref([
    {name: 'Attendees ascending', value: 'attendeesASC'},
    {name: 'Attendees descending', value: 'attendeesDESC'},
    {name: 'Date ascending', value: 'dateASC'},
    {name: 'Date descending', value: 'dateDESC'}
])

const selectedEventType = ref();

const eventTypeOptions = ref([
    {name: 'Online', value: 'online'},
    {name: 'Offline', value: 'offline'}
]);

const selectedEventContinent = ref();
const eventContinentOptions = ref( [
    { value: 'AF', name: 'Africa' },
    { value: 'AN', name: 'Antarctica' },
    { value: 'AS', name: 'Asia' },
    { value: 'EU', name: 'Europe' },
    { value: 'NA', name: 'North America' },
    { value: 'OC', name: 'Oceania' },
    { value: 'SA', name: 'South America' }
])

const eventCountryContent = ref()
const eventCountryMatching = ref(true)
const selectedEventCountry = ref('default');
const filteredCountries = ref();

const updateCountryOptions = (event) => {
    if (!event.query.trim().length) {
        filteredCountries.value = [...eventCountryOptions.value];
    } else {
        filteredCountries.value = eventCountryOptions.value.filter((country) => {
            return country.name.toLowerCase().startsWith(event.query.toLowerCase());
        });
    }
    if(filteredCountries.value[0]){
        selectedEventCountry.value = filteredCountries.value[0]
        eventCountryMatching.value = true
    }else{
        selectedEventCountry.value = null
        eventCountryMatching.value = false
    }
    fetchEvents()
}

const selectEventCountryFilter = function(country){
    selectedEventCountry.value = country
    eventCountryMatching.value = true
    setEventCountryContent()
    fetchEvents()
}

const setEventCountryContent = function(){
    if (eventCountryMatching.value && eventCountryContent.value !== ''){
        eventCountryContent.value = selectedEventCountry.value
    }else{
        eventCountryContent.value =''
        selectedEventCountry.value = 'default'
        fetchEvents()
    }
}

const eventCountryOptions = ref([]);

const fetchCountries = async function(){
    try {
        eventCountryContent.value = null
        eventCountryMatching.value = false
        selectedEventCountry.value = 'default'

        const continent = selectedEventContinent.value?.value ?? 'default';
        const response  = await axios.get('/api/countries-filter?continent=' + continent);
        eventCountryOptions.value = response.data.data
    }catch (error){
        console.error(error)
    }
}

const events = ref({})
const fetchEvents = async function (page=1) {
    try {
        currentPage.value = page
        loading.value = true
        const type = selectedEventType.value?.value ?? 'default';
        const ordering = selectedOrdering.value?.value ?? 'default';
        const continent = selectedEventContinent.value?.value ?? 'default';
        let country = selectedEventCountry.value
        if (country && country.value){
            country = country.value
        }
        const response = await axios.get('/api/events?page=' + page + '&continent=' + continent + '&country=' + country + '&type='+ type + '&ordering=' + ordering);
        events.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error(error);
    }
}

const loadData = async function(){
    loading.value = true;
    await fetchCountries()
    await fetchEvents()

    loading.value = false
}

loadData()

</script>

<template>
    <div id="event-filters">
        <div class="event-filter">
            <Dropdown v-model="selectedOrdering" @change="fetchEvents()" :options="orderByOptions" optionLabel="name" showClear placeholder="Default ordering"/>
        </div>
        <div class="event-filter">
            <Dropdown v-model="selectedEventType" @change="fetchEvents()" :options="eventTypeOptions" optionLabel="name" showClear placeholder="All event types"/>
        </div>
        <div class="event-filter">
            <Dropdown v-model="selectedEventContinent" @change="loadData()" :options="eventContinentOptions" optionLabel="name" showClear placeholder="All continents"/>
        </div>
        <div class="event-filter">
            <AutoComplete v-model="eventCountryContent" :suggestions="filteredCountries" optionLabel="name" @complete="updateCountryOptions" @focusout="setEventCountryContent" @item-select="selectEventCountryFilter($event.value)" empty-search-message=" " empty-selection-message=" " placeholder="Country" />
        </div>
    </div>
    <template v-if="!loading">
        <template v-if="events.data.length > 0">
            <div class="event-container">
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
                        <div class="event-attendees"><Chip :label=event.attendees.toString() icon="pi pi-users"></Chip></div>
                        <div v-if="!event.is_online" class="event-location"><Chip :label="event.address.name" icon="pi pi-map-marker"></Chip></div>
                        <div v-else class="event-location"><Chip label="Online" icon="pi pi-globe"></Chip></div>
                        <div class="event-datetime"><Chip :label="event.timezone_start_date_time + ' / ' + event.timezone_end_date_time + ' ' + event.timezone" icon="pi pi-clock"></Chip></div>
                    </template>
                </Card>
            </div>
            <VueAwesomePaginate
                :total-items="events.meta.total"
                :items-per-page="events.meta.per_page"
                :max-pages-shown="3"
                v-model="currentPage"
                :on-click="fetchEvents"
            />
        </template>
        <template v-else>
            <h1>No events</h1>
        </template>

    </template>
    <template v-if="loading">
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

.pagination-container {
    column-gap: 10px;
    display: flex!important;
    justify-content: center;
}
.paginate-buttons {
    height: 40px;
    width: 40px;
    border-radius: 20px;
    cursor: pointer;
    background-color: #dee2e6 ;
    border: none;
    color: black;
    padding: 0;
}
.paginate-buttons:hover {
    background-color: #d8d8d8;
}
.active-page {
    background-color: #3498db;
    border: 1px solid #3498db;
    color: white;
}
.active-page:hover {
    background-color: #2988c8;
}
.back-button:active,
.next-button:active {
    background-color: #c4c4c4;
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
    .paginate-buttons {
        height: 30px;
        width: 30px;
    }
}

</style>
