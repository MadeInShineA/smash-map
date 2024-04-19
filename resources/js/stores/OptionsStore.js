import {defineStore} from "pinia"
import {ref} from "vue";

export const useOptionsStore = defineStore('option', function (){
    const gameOptions = ref([
        {name: '64', id: 4},
        {name: 'Melee', id: 1},
        {name: 'Brawl', id: 5},
        {name: 'Project M', id: 2},
        {name: 'Project +', id: 33602},
        {name: '3DS / WiiU', id: 3},
        {name: 'Ultimate', id: 1386},
    ])

    const eventTypeOptions = ref([
        {name: 'All event types', value: 'default'},
        {name: 'Online', value: 'online'},
        {name: 'Offline', value: 'offline'}
    ]);

    const eventOrderByOptions = ref([
        {name: 'Sort by ID', value: 'default'},
        {name: 'Attendees ascending', value: 'attendeesASC'},
        {name: 'Attendees descending', value: 'attendeesDESC'},
        {name: 'Date ascending', value: 'dateASC'},
        {name: 'Date descending', value: 'dateDESC'}
    ])

    const addressTypeOptions = ref([
        {name: 'All marker types', value: 'default'},
        {name: 'Events', value: 'events'},
        {name: 'Users', value: 'users'},
        {name: 'Modders', value: 'modders'},
    ]);


    const continentOptions = ref( [
        { name: 'Africa', code: 'AF' },
        { name: 'Antarctica', code: 'AN' },
        { name: 'Asia', code: 'AS' },
        { name: 'Europe', code: 'EU' },
        { name: 'North America', code: 'NA' },
        { name: 'Oceania', code: 'OC' },
        { name: 'South America', code: 'SA' }
    ])

    const notificationOptions = ref([
        {name: 'Distance notifications', value: 'distanceNotifications'},
        {name: 'Attendees notifications', value: 'attendeesNotifications'},
        {name: 'Time notifications', value: 'timeNotifications'},
    ]);

    return {
        gameOptions,
        eventTypeOptions,
        eventOrderByOptions,
        addressTypeOptions,
        continentOptions,
        notificationOptions
    }

})
