import {defineStore} from "pinia"
import {ref} from "vue";
import {useAddressFiltersStore} from "../stores/AddressFiltersStore.js";
import {useEventFiltersStore} from "../stores/EventFiltersStore.js";
import axios from "axios";
import {router} from "@/router.js";
import {useAxios} from "@vueuse/integrations/useAxios";
import Swal from "sweetalert2";

export const useUserStore = defineStore('user', function (){

    const addressesFilterStore = useAddressFiltersStore()
    const eventsFilterStore = useEventFiltersStore()

    // TODO Sync the notifications count with the server
    const notificationsCount = ref(0)
    const user = ref(JSON.parse(window.localStorage.getItem('userData')))

    function getSettings(userId, darkMode){
        return axios.get('/api/users/' + userId + '/settings').then((response) =>{
            return response.data
         }).catch((error) => {
            console.log(error)
            const alertBackground = darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = darkMode ? '#FFFFFF' : '#1C1B22'
            Swal.fire({
                title: 'Error',
                text: error.response.data.message,
                icon: 'error',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                if (error.response.status === 401) {
                    router.push('/')
                }
            })

        })
    }

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
        if (router.currentRoute.value.path === '/map') {
            addressesFilterStore.fetchAddressesWithFilters()
        } else if (router.currentRoute.value.path === '/events') {
            eventsFilterStore.fetchEventsWithFilters()
        }
        return response
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
        if(router.currentRoute.value.path === '/map'){
            addressesFilterStore.fetchAddressesWithFilters()
        }
        else if(router.currentRoute.value.path === '/events'){
            eventsFilterStore.fetchEventsWithFilters()
        }
        return response
    }

    async function logout() {
        const response = await axios.post('/api/logout');
        unsubscribeToNotifications()
        localStorage.removeItem('userData');
        user.value = null;
        if(router.currentRoute.value.path === '/settings'){
            router.push('/')
        }
        return response
    }

    async function forgotPassword(email) {
        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        return axios.post('/api/forgot-password', {"email": email}, header)
    }

    async function resetPassword(resetData) {
        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        return axios.post('/api/reset-password', resetData, header)
    }

    async function saveSettings(settings){
        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        return axios.post('/api/users/' + user.value.id + '/settings', settings, header)

    }

    return {
        user,
        notificationsCount,
        setUser,
        getSettings,
        subscribeToNotifications,
        login,
        register,
        logout,
        forgotPassword,
        resetPassword,
        saveSettings
    }
})
