import {defineStore} from "pinia"
import {useAxios} from "@vueuse/integrations/useAxios";
import {ref} from "vue";
import Swal from "sweetalert2";
import {useDark} from "@vueuse/core";

export const useEventsStore = defineStore('events', function (){
    const { data: events, isFinished: eventsFetched, execute: fetchEvents } = useAxios('/api/events',{}, {immediate: false})


    const subscriptionLoading = ref(false)


    function handleEventSubscription(event, darkMode){
        const title = event.user_subscribed ? 'Unsubscribed!' : 'Subscribed!'
        const message = event.user_subscribed ? 'You are successfully unsubscribed' : 'You are successfully subscribed'
        const alertBackground = darkMode ? '#1C1B22' : '#FFFFFF'
        const alertColor = darkMode ? '#FFFFFF' : '#1C1B22'
        Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            background: alertBackground,
            color: alertColor,
            timer: 1500,
            showConfirmButton: false
        })
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
