import {defineStore} from "pinia"
import {useAxios} from "@vueuse/integrations/useAxios";

export const useEventsStore = defineStore('events', function (){
    const { data: events, isFinished: eventsFetched, execute: fetchEvents } = useAxios('/api/events')

    return {
        events,
        eventsFetched,
        fetchEvents
    }
})
