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

    // TODO Move this 2 to a store with the available options ?
    const {data:addressesCountryOptions, isFinished: countriesFetched, execute: fetchCountries} = useAxios('/api/countries')
    const { data: addressesCharactersOptions, isFinished: charactersFetched, execute: fetchCharacters } = useAxios('/api/characters')

    const selectedAddressGames = ref([]);
    const selectedAddressTypes = ref('default');
    const selectedAddressDates = ref([])
    const selectedAddressContinents = ref([]);
    const selectedAddressCountries = ref([]);
    const selectedAddressName = ref('')
    const selectedAddressCharacters = ref([])


    const countriesWatch = watchPausable([selectedAddressCountries], function([countries]){
        countries = countries.length > 0 ? countries.join(',') : 'default'
        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users' ){
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
        const characters = selectedAddressCharacters.value.length > 0 ? selectedAddressCharacters.value.join(',') : 'default'

        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate, characters}})
    }, {immediate: false})

    const charactersWatch = watchPausable([selectedAddressCharacters], function([characters]){
        characters = characters.length > 0 ? characters.join(',') : 'default'
        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users' ){
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
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'

        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate, characters}})
    }, {immediate: false})

    watch([selectedAddressContinents], function([continents]){
        continents = continents.length > 0 ? continents.join(',') : 'default'

        // TODO Find how to avoid the double fetch + not have the hold countries when removing a continent
        // countriesWatch.pause()
        fetchCountries({params: {continents}}).then(() => {
            // countriesWatch.resume()
        })



        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users'){
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
        const countries = selectedAddressCountries.value.length > 0 ? selectedAddressCountries.value.join(',') : 'default'
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'
        const characters = selectedAddressCharacters.value.length > 0 ? selectedAddressCharacters.value.join(',') : 'default'


        addressesStore.fetchAddresses({ params: { games, type, continents, countries, name, startDate, endDate, characters}})
    })

    watch([selectedAddressGames], function([games]){
        games = games.length > 0 ? games.join(',') : 'default'

        // TODO Find how to avoid the double fetch + not have the hold characters when removing a game
        // charactersWatch.pause()
        fetchCharacters({params: {games}}).then(() => {
            // charactersWatch.resume()
        })

        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users'){
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

        const type = selectedAddressTypes.value
        const continents = selectedAddressContinents.value.length > 0 ? selectedAddressContinents.value.join(',') : 'default'
        const countries = selectedAddressCountries.value.length > 0 ? selectedAddressCountries.value.join(',') : 'default'
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'
        const characters = selectedAddressCharacters.value.length > 0 ? selectedAddressCharacters.value.join(',') : 'default'

        addressesStore.fetchAddresses({ params: { games, type, continents, countries, name, startDate, endDate, characters}})
    })


    watch([selectedAddressTypes, selectedAddressDates], function([type, dates]){
        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users'){
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

        const games = selectedAddressGames.value.length > 0 ? selectedAddressGames.value.join(',') : 'default'
        const continents = selectedAddressContinents.value.length > 0 ? selectedAddressContinents.value.join(',') : 'default'
        const countries = selectedAddressCountries.value.length > 0 ? selectedAddressCountries.value.join(',') : 'default'
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'
        const characters = selectedAddressCharacters.value.length > 0 ? selectedAddressCharacters.value.join(',') : 'default'

        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate, characters}})
    }, {immediate: false})

    watch(addressesCountryOptions, function(availableCountries, oldValue){
        if (oldValue && availableCountries.length !== 0){
            const availableCountryCodes = availableCountries.data.map(country => country.code)
            selectedAddressCountries.value = selectedAddressCountries.value.filter(code => availableCountryCodes.includes(code))
        }
    }, {immediate: false})

    watch(addressesCharactersOptions, function(availableCharacters, oldValue){
        if (oldValue && availableCharacters.length !== 0){
            let availableCharacterIds = []
            availableCharacters.data.forEach((game) => {
                game.characters.forEach((character) => {
                    availableCharacterIds.push(character.id)
                })
            })
            selectedAddressCharacters.value = selectedAddressCharacters.value.filter(id => availableCharacterIds.includes(id))
        }
    }, {immediate: false})


    watchDebounced([selectedAddressName], function([name]){
        name = name !== '' ? name : 'default'

        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users'){
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
        const characters = selectedAddressCharacters.value.length > 0 ? selectedAddressCharacters.value.join(',') : 'default'
        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate, characters}})

    }, { immediate: false, debounce: 600, maxWait: 2000 })

    // TODO Use this function in the different watch functions
    function fetchAddressesWithFilters() {

        let startDate
        let endDate
        if(selectedAddressDates.value && selectedAddressTypes.value !== 'users'){
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
        const name = selectedAddressName.value !== '' ? selectedAddressName.value : 'default'
        const characters = selectedAddressCharacters.value.length > 0 ? selectedAddressCharacters.value.join(',') : 'default'
        addressesStore.fetchAddresses({ params: {games, type, continents, countries, name, startDate, endDate, characters}})
    }

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
        selectedAddressName,
        selectedAddressCharacters,
        addressesCharactersOptions,
        charactersFetched,
        fetchCharacters,
        fetchAddressesWithFilters
    }
})
