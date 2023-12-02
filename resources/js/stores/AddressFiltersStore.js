import {defineStore} from "pinia";
import {ref, watch} from "vue"
import {useAxios} from "@vueuse/integrations/useAxios";
import {useDateFormat, watchDebounced, watchPausable} from "@vueuse/core";
import {useAddressesStore} from "../stores/AddressesStore.js";

export const useAddressFiltersStore = defineStore('addressFilters', function (){
    const addressesStore = useAddressesStore()

    const addressTypeOptions = ref([
        {name: 'All marker types', value: 'default'},
        {name: 'Events', value: 'events'},
        {name: 'Users', value: 'users'}
    ]);


    const addressContinentOptions = ref( [
        { name: 'Africa', code: 'AF' },
        { name: 'Antarctica', code: 'AN' },
        { name: 'Asia', code: 'AS' },
        { name: 'Europe', code: 'EU' },
        { name: 'North America', code: 'NA' },
        { name: 'Oceania', code: 'OC' },
        { name: 'South America', code: 'SA' }
    ])

    const addressGameOptions = ref([
        {name: '64', id: '4'},
        {name: 'Melee', id: '1'},
        {name: 'Brawl', id: '5'},
        {name: 'Project M', id: '2'},
        {name: 'Project +', id: '33602'},
        {name: '3DS / WiiU', id: '3'},
        {name: 'Ultimate', id: '1386'},
    ])

    const {data:addressesCountryOptions, isFinished: countriesFetched, execute: fetchCountries} = useAxios('/api/addresses-countries-filter')

    const { data: addressesCharactersOptions, isFinished: charactersFetched, execute: fetchCharacters } = useAxios('/api/addresses-characters-filter')

    const selectedAddressGames = ref([]);
    const selectedAddressTypes = ref('default');
    const selectedAddressDates = ref([])
    const selectedAddressContinents = ref([]);
    const selectedAddressCountries = ref([]);
    const selectedAddressName = ref('')
    const selectedAddressCharacters = ref([])


    const {pause: pauseContinentsWatch, resume: resumeContinentsWatch } = watchPausable([selectedAddressContinents], function([continents]){
        continents = continents.length > 0 ? continents.join(',') : 'default'
        fetchCountries({params: {continents}})

        let startDate
        let endDate
        if(selectedAddressDates.value){
            startDate = selectedAddressDates.value[0]
            if (startDate){
                startDate = useDateFormat(startDate,'YYYY-MM-DD')
                startDate = startDate.value
            }else{
                startDate = 'default'
            }

            endDate = selectedAddressDates.value[1]
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

        const games = selectedAddressGames.value.length > 0 ? selectedAddressGames.value.join(',') : 'default'
        const type = selectedAddressTypes.value.value
        const countries = selectedAddressCountries.value.length > 0 ? selectedAddressCountries.value.join(',') : 'default'
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'

        addressesStore.fetchAddresses({ params: { games, type, continents, countries, name, startDate, endDate}})


    }, {immediate: false})

    const {pause: pauseCountriesWatch, resume: resumeCountriesWatch} = watchPausable([selectedAddressCountries], function([countries]){
        countries = countries.length > 0 ? countries.map(country => country.code).join(',') : 'default'

        let startDate
        let endDate
        if(selectedAddressDates.value){
            startDate = selectedAddressDates.value[0]
            if (startDate){
                startDate = useDateFormat(startDate,'YYYY-MM-DD')
                startDate = startDate.value
            }else{
                startDate = 'default'
            }

            endDate = selectedAddressDates.value[1]
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

        const games = selectedAddressGames.value.length > 0 ? selectedAddressGames.value.join(',') : 'default'
        const type = selectedAddressTypes.value
        const continents = selectedAddressContinents.value.length > 0 ? selectedAddressContinents.value.join(',') : 'default'
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'

        addressesStore.fetchAddresses({ params: { page: 1, games, type, continents, countries, name, startDate, endDate}})
    }, {immediate: false})


    watch([selectedAddressGames, selectedAddressTypes, selectedAddressDates], function([games, type, dates]){
        if (type === 'online'){

            pauseContinentsWatch()
            selectedAddressContinents.value = []
            resumeContinentsWatch()

            addressesCountryOptions.value = []
        }

        let startDate
        let endDate
        if(selectedAddressDates.value){
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

        games = games.length > 0 ? games.join(',') : 'default'
        const continents = selectedAddressContinents.value.length > 0 ? selectedAddressContinents.value.join(',') : 'default'
        const countries = selectedAddressCountries.value.length > 0 ? selectedAddressCountries.value.join(',') : 'default'
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'

        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate}})
    }, {immediate: false})

    watch(addressesCountryOptions, function(availableCountries, oldValue){
        if (oldValue && availableCountries.length !== 0){
            const availableCountryCodes = availableCountries.data.map(country => country.code)
            pauseCountriesWatch()
            selectedAddressCountries.value = selectedAddressCountries.value.filter(code => availableCountryCodes.includes(code))
            resumeCountriesWatch()
        }
    }, {immediate: false})

    watchDebounced([selectedAddressName], function([name]){
        name = name !== '' ? name : 'default'

        let startDate
        let endDate
        if(selectedAddressDates.value){
            startDate = selectedAddressDates.value[0]
            if (startDate){
                startDate = useDateFormat(startDate,'YYYY-MM-DD')
                startDate = startDate.value
            }else{
                startDate = 'default'
            }

            endDate = selectedAddressDates.value[1]
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

        const games = selectedAddressGames.value.length > 0 ? selectedAddressGames.value.join(',') : 'default'
        const type = selectedAddressTypes.value
        const continents = selectedAddressContinents.value.length > 0 ? selectedAddressContinents.value.join(',') : 'default'
        const countries = selectedAddressCountries.value.length > 0 ? selectedAddressCountries.value.join(',') : 'default'
        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate}})

    }, { immediate: false, debounce: 600, maxWait: 2000 })

    return {
        addressTypeOptions,
        addressContinentOptions,
        addressGameOptions,
        addressesCountryOptions,
        countriesFetched,
        selectedAddressGames,
        selectedAddressTypes,
        selectedAddressDates,
        selectedAddressContinents,
        selectedAddressCountries,
        selectedAddressName
    }
})
