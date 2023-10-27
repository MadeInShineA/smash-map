import {defineStore} from "pinia";
import {ref, watch} from "vue"
import {useAxios} from "@vueuse/integrations/useAxios";
import {useDateFormat, watchDebounced, watchPausable} from "@vueuse/core";
import {useEventsStore} from "../stores/EventsStore.js";

export const useEventFiltersStore = defineStore('eventFilters', function (){
    const eventsStore = useEventsStore()

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

    const eventGameOptions = ref([
        {name: '64', value: '4'},
        {name: 'Melee', value: '1'},
        {name: 'Brawl', value: '5'},
        {name: 'Project M', value: '2'},
        {name: 'Project +', value: '33602'},
        {name: '3DS / WiiU', value: '3'},
        {name: 'Ultimate', value: '1386'},
    ])

    const {data: eventCountryOptions, isFinished: countriesFetched, execute: fetchCountries} = useAxios('/api/countries-filter')

    const currentPage = ref(1);
    const selectedOrderBy = ref({name: 'Default sort', value: 'default'});
    const selectedEventGames = ref([]);
    const selectedEventTypes = ref({name: 'Default type', value: 'default'});
    const selectedEventDates = ref([])
    const selectedEventContinents = ref([]);
    const selectedEventCountries = ref([]);
    const selectedEventName = ref('')

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
        const type = selectedEventTypes.value.value
        const orderBy = selectedOrderBy.value.value
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page, games, type, orderBy, continents, countries, name, startDate, endDate}})
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
        const type = selectedEventTypes.value.value
        const orderBy = selectedOrderBy.value.value
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})


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
        const type = selectedEventTypes.value.value
        const orderBy = selectedOrderBy.value.value
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})
    }, {immediate: false})


    watch([selectedOrderBy, selectedEventGames, selectedEventTypes, selectedEventDates], function([{value: orderBy} , games, {value: type}, dates]){
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

        eventsStore.fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})
    }, {immediate: false})

    watch(eventCountryOptions, function(availableCountries, oldValue){
        //TODO Directly add the data to availableCountries
        if (oldValue && availableCountries.length !== 0){
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
        const type = selectedEventTypes.value.value
        const orderBy = selectedOrderBy.value.value
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.map(continent => continent.code).join(',') : 'default'
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.map(country => country.code).join(',') : 'default'
        eventsStore.fetchEvents({ params: { page, games, type, orderBy, continents, countries, name, startDate, endDate}})

    }, { immediate: false, debounce: 600, maxWait: 2000 })

    return {
        eventTypeOptions,
        eventContinentOptions,
        eventGameOptions,
        eventCountryOptions,
        countriesFetched,
        currentPage,
        selectedOrderBy,
        selectedEventGames,
        selectedEventTypes,
        selectedEventDates,
        selectedEventContinents,
        selectedEventCountries,
        selectedEventName
    }
})
