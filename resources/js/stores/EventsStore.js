import {defineStore} from "pinia"
import {useAxios} from "@vueuse/integrations/useAxios";
import {ref} from "vue";
import Swal from "sweetalert2";
import {useDark} from "@vueuse/core";

export const useEventsStore = defineStore('events', function (){
    const { data: events, isFinished: eventsFetched, execute: fetchEvents } = useAxios('/api/events',{}, {immediate: false})


    const subscriptionLoading = ref(false)

    function handleEventSubscription(event, darkMode){
        const title = event.user_subscribed ? 'Unfollowed!' : 'Followed!'
        const message = event.user_subscribed ? 'Event unfollowed with success!' : 'Event followed with success!'
        const alertBackground = darkMode ? '#1C1B22' : '#FFFFFF'
        const alertColor = darkMode ? '#FFFFFF' : '#1C1B22'
        const url = event.user_subscribed ? '/api/events/' + event.id + '/unsubscribe' : '/api/events/' + event.id + '/subscribe'
        subscriptionLoading.value = true
        axios.post(url)
            .then(() => {
                event.user_subscribed = !event.user_subscribed
                subscriptionLoading.value = false
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'success',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
            })
            .catch((error) => {
                Swal.fire({
                    title: 'Error',
                    text: error.response.data.message,
                    icon: 'error',
                    background: alertBackground,
                    color: alertColor,
                    timer: 2000,
                    showConfirmButton: false
                })
                subscriptionLoading.value = false
            })

    }

    const {data: gamesStatistics, isFinished: gamesStatisticsFetched, execute: fetchGameStatistics} = useAxios('/api/events/statistics?type=games', {}, {immediate: false})
    const {data: monthsStatistics, isFinished: monthsStatisticsFetched, execute: fetchMonthsStatistics} = useAxios('/api/events/statistics?type=months', {}, {immediate: false})


    return {
        events,
        eventsFetched,
        fetchEvents,
        subscriptionLoading,
        handleEventSubscription,
        gamesStatistics,
        gamesStatisticsFetched,
        fetchGameStatistics,
        monthsStatistics,
        monthsStatisticsFetched,
        fetchMonthsStatistics
    }
})
