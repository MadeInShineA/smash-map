import {defineStore} from "pinia"
import {useAxios} from "@vueuse/integrations/useAxios";
import {ref} from "vue";

export const useEventsStore = defineStore('events', function (){
    const { data: events, isFinished: eventsFetched, execute: fetchEvents } = useAxios('/api/events')


    const subscriptionLoading = ref(false)
    function handleEventSubscription(event){
        subscriptionLoading.value = true
        if (event.user_subscribed){
            axios.post('/api/events/' + event.id + '/unsubscribe')
            event.user_subscribed = false
        }else{
            axios.post('/api/events/' + event.id + '/subscribe')
            event.user_subscribed = true
        }
        subscriptionLoading.value = false
    }

    return {
        events,
        eventsFetched,
        fetchEvents,
        subscriptionLoading,
        handleEventSubscription
    }
})
