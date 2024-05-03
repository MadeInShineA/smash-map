import {defineStore} from "pinia"
import {reactive, ref} from "vue";
import {useAddressFiltersStore} from "../stores/AddressFiltersStore.js";
import {useEventFiltersStore} from "../stores/EventFiltersStore.js";
import axios from "axios";
import {router} from "@/router.js";
import Swal from "sweetalert2";


export const useUserStore = defineStore('user', function (){

    const addressesFilterStore = useAddressFiltersStore()
    const eventsFilterStore = useEventFiltersStore()

    const initialUserState = {
        id: null,
        profilePicture: '',
        settings: {
            hasDistanceNotifications: false,
            distanceNotificationsRadius: 0,
            address: { lat: 40.713956, lng: -38.716136 }
        }
    }

    const user = reactive({
        data: JSON.parse(localStorage.getItem('userData')) || initialUserState
    })

    const notificationsCount = ref(0)
    const notificationsCountFetched = ref(false)
    if (user.data.id) {
        fetchNotificationsCount()
    }

    function fetchNotificationsCount(){
        axios.get('/api/users/' + user.data.id + '/notifications/count').then((response) => {
            notificationsCount.value = parseInt(response.data.data)
            notificationsCountFetched.value = true
        })
    }

    function getSettings(userId, darkMode){
        return axios.get('/api/users/' + userId + '/settings').then((response) =>{
            // user.value.settings = response.data.data
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

    function setUser(userData){
        localStorage.setItem('userData', JSON.stringify(userData));
        user.data = userData
    }


    const toast = ref()
    function subscribeToNotifications(){
        Echo.private(`notifications.` + user.data.id).listen('NotificationEvent', (e) => {
            console.log("Notification received", e)
            const audio = new Audio('/storage/audios/notification-sound.mp3');
            audio.volume = 0.2
            audio.play()
            toast.value.add({severity: 'info', icon: e.gameImage , summary: e.notificationType + " for : " + e.gameName, detail: e.message, life: 7000});
            notificationsCount.value = notificationsCount.value + 1
        });
    }

    function unsubscribeToNotifications(){
        Echo.leave(`notifications.` + user.data.id)
    }
    async function login(loginUser) {

        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        const response = await axios.post('/api/login', loginUser, header)
        setUser(response.data.data.user)
        localStorage.setItem('accessToken', response.data.data.token)
        localStorage.setItem('tokenTime', new Date().toString());
        fetchNotificationsCount()
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
        setUser(response.data.data.user)
        localStorage.setItem('accessToken', response.data.data.token)
        localStorage.setItem('tokenTime', new Date().toString());
        subscribeToNotifications()
        fetchNotificationsCount()
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
        user.data = initialUserState
        notificationsCount.value = 0
        if(router.currentRoute.value.path === '/settings' || router.currentRoute.value.path === '/notifications'){
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
        const response = await axios.post('/api/users/' + user.data.id + '/settings', settings, header)
        if(response.status === 200){
            if(user.data.profilePicture.includes('?')){
                user.data.profilePicture = user.data.profilePicture.substring(0, user.data.profilePicture.indexOf('?')) + '?time=' + new Date().getTime()
            }else{
                user.data.profilePicture = user.data.profilePicture + '?time=' + new Date().getTime()
            }
            user.data.settings.hasDistanceNotifications = settings.notifications.includes('hasDistanceNotifications')
            user.data.settings.distanceNotificationsRadius = settings.distanceNotificationsRadius
            user.data.settings.address = {lat: settings.address.latitude, lng: settings.address.longitude}
            localStorage.setItem('userData', JSON.stringify(user.data))

        }
        return response
    }

    async function updateDistanceNotificationsRadius(distanceNotificationsRadius){
        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        const response = await axios.post('/api/users/' + user.data.id + '/settings/update-distance-notifications-radius', {distanceNotificationsRadius: distanceNotificationsRadius}, header)
        if(response.status === 200){
            user.data.settings.distanceNotificationsRadius = distanceNotificationsRadius
            localStorage.setItem('userData', JSON.stringify(user.data))
        }
        return response
    }

    function getNotifications(userId, darkMode){
        return axios.get('/api/users/' + userId + '/notifications').then((response) =>{
            if(response.status === 200){
                notificationsCount.value = 0
            }
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

    async function deleteAccount() {
        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        const response = await axios.delete('/api/users/' + user.data.id, header)
        if(response.status === 200){
            localStorage.removeItem('userData');
            user.data = initialUserState
            notificationsCount.value = 0
        }
        if(router.currentRoute.value.path === '/settings' || router.currentRoute.value.path === '/notifications'){
            router.push('/')
        }
        return response
    }




    return {
        user,
        notificationsCount,
        notificationsCountFetched,
        setUser,
        getSettings,
        subscribeToNotifications,
        toast,
        login,
        register,
        logout,
        deleteAccount,
        forgotPassword,
        resetPassword,
        saveSettings,
        updateDistanceNotificationsRadius,
        getNotifications,
    }
})
