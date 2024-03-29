import {defineStore} from "pinia"
import {ref} from "vue";
import {useAddressFiltersStore} from "../stores/AddressFiltersStore.js";
import {useEventFiltersStore} from "../stores/EventFiltersStore.js";
import axios from "axios";
export const useUserStore = defineStore('user', function (){

    const addressesFilterStore = useAddressFiltersStore()
    const eventsFilterStore = useEventFiltersStore()

    // TODO Sync the notifications count with the server
    const notificationsCount = ref(10)
    const user = ref(JSON.parse(window.localStorage.getItem('userData')))

    function setUser(){
        user.value = JSON.parse(window.localStorage.getItem('userData'));
    }

    function subscribeToNotifications(){
        Echo.private(`notifications.` + user.value.id).listen('NotificationEvent', (e) => {
            console.log("Notification received", e)
            notificationsCount.value = (parseInt(notificationsCount.value) + 1).toString()
        });
    }

    function unsubscribeToNotifications(){
        Echo.leave(`notifications.` + user.value.id)
    }
    async function login(loginUser) {

        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        const response = await axios.post('/api/login', loginUser, header)
        localStorage.setItem('userData', JSON.stringify(response.data.data.user));
        localStorage.setItem('accessToken', response.data.data.token)
        localStorage.setItem('tokenTime', new Date().toString());
        setUser()
        subscribeToNotifications()
        addressesFilterStore.fetchAddressesWithFilters()
        eventsFilterStore.fetchEventsWithFilters()
    }



    async function register(registerUser) {

        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }

        const response = await axios.post('/api/register', registerUser, header)
        localStorage.setItem('userData', JSON.stringify(response.data.data.user));
        localStorage.setItem('accessToken', response.data.data.token)
        localStorage.setItem('tokenTime', new Date().toString());
        setUser()
        subscribeToNotifications()
        addressesFilterStore.fetchAddressesWithFilters()
        eventsFilterStore.fetchEventsWithFilters()
    }

    async function logout() {
        await axios.post('/api/logout');
        unsubscribeToNotifications()
        localStorage.removeItem('userData');
        user.value = null;
    }

    return {
        user,
        notificationsCount,
        setUser,
        subscribeToNotifications,
        login,
        register,
        logout,
    }
})
