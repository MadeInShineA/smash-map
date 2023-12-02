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
        {name: '64', id: '4'},
        {name: 'Melee', id: '1'},
        {name: 'Brawl', id: '5'},
        {name: 'Project M', id: '2'},
        {name: 'Project +', id: '33602'},
        {name: '3DS / WiiU', id: '3'},
        {name: 'Ultimate', id: '1386'},
    ])

    const {data: eventCountryOptions, isFinished: countriesFetched, execute: fetchCountries} = useAxios('/api/countries-filter')

    const currentPage = ref(1);
    const selectedOrderBy = ref('default');
    const selectedEventGames = ref([]);
    const selectedEventTypes = ref('default');
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

        const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.join(',') : 'default'
        const type = selectedEventTypes.value
        const orderBy = selectedOrderBy.value
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.join(',') : 'default'
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page, games, type, orderBy, continents, countries, name, startDate, endDate}})
    })

    const {pause: pauseContinentsWatch, resume: resumeContinentsWatch } = watchPausable([selectedEventContinents], function([continents]){
        continents = continents.length > 0 ? continents.join(',') : 'default'
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
        const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.join(',') : 'default'
        const type = selectedEventTypes.value
        const orderBy = selectedOrderBy.value
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})


    }, {immediate: false})

    const {pause: pauseCountriesWatch, resume: resumeCountriesWatch} = watchPausable([selectedEventCountries], function([countries]){
        countries = countries.length > 0 ? countries.join(',') : 'default'

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

        const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.join(',') : 'default'
        const type = selectedEventTypes.value
        const orderBy = selectedOrderBy.value
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})
    }, {immediate: false})


    watch([selectedOrderBy, selectedEventGames, selectedEventTypes, selectedEventDates], function([ orderBy , games,  type, dates]){
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

        games = games.length > 0 ? games.join(',') : 'default'
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.join(',') : 'default'
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.join(',') : 'default'
        const name = selectedEventName.value !== '' ? selectedEventName.value : 'default'

        eventsStore.fetchEvents({ params: { page: 1, games, type, orderBy, continents, countries, name, startDate, endDate}})
    }, {immediate: false})

    watch(eventCountryOptions, function(availableCountries, oldValue){
        if (oldValue && availableCountries.length !== 0){
            const availableCountryCodes = availableCountries.data.map(country => country.code)
            pauseCountriesWatch()
            selectedEventCountries.value = selectedEventCountries.value.filter(code => availableCountryCodes.includes(code))
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
        const games = selectedEventGames.value.length > 0 ? selectedEventGames.value.join(',') : 'default'
        const type = selectedEventTypes.value
        const orderBy = selectedOrderBy.value
        const continents = selectedEventContinents.value.length > 0 ? selectedEventContinents.value.join(',') : 'default'
        const countries = selectedEventCountries.value.length > 0 ? selectedEventCountries.value.join(',') : 'default'
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
