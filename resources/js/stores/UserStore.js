import {defineStore} from "pinia"
import {reactive, ref} from "vue";
import {useAddressFiltersStore} from "../stores/AddressFiltersStore.js";
import {useEventFiltersStore} from "../stores/EventFiltersStore.js";
import axios from "axios";
import {router} from "@/router.js";
import Swal from "sweetalert2";
import * as PusherPushNotifications from "@pusher/push-notifications-web";


export const useUserStore = defineStore('user', function (){

    const addressesFilterStore = useAddressFiltersStore()
    const eventsFilterStore = useEventFiltersStore()

    // const beamsClient = new PusherPushNotifications.Client({
    //     instanceId: import.meta.env.VITE_PUSHER_BEAMS_INSTANCE_ID,
    // });

    const initialUserState = {
        id: null,
        profilePicture: {
            url: ''
        },
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

    function setUser(userData){
        localStorage.setItem('userData', JSON.stringify(userData));
        user.data = userData
    }

    const toast = ref()
    function subscribeToNotifications(){
        Echo.private(`notifications.` + user.data.id).listen('NotificationEvent', (e) => {
            const audio = new Audio('/storage/audios/notification-sound.mp3');
            audio.volume = 0.2
            audio.play()
            toast.value.add({severity: 'info', icon: e.imageUrl , summary: e.notificationType + " for : " + e.gameTitle, detail: e.message, life: 7000});
            if(router.currentRoute.value.path !== '/notifications') {
                notificationsCount.value = notificationsCount.value + 1
            }
        });
    }

    function unsubscribeToNotifications(){
        Echo.leave(`notifications.` + user.data.id)
    }

    function subscribeToPushNotifications(){

        const beamsTokenProvider = new PusherPushNotifications.TokenProvider({
            url: import.meta.env.VITE_PUSHER_BEAMS_AUTH_ENDPOINT,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
            },
        });

        beamsClient
            .start()

            // App.Models.User.1 is required for the laravel-notification-channels/pusher-push-notifications package
            .then(() => beamsClient.setUserId("App.Models.User." + user.data.id, beamsTokenProvider))
            .then(() => { console.log('Beams client has started') })
            .catch(console.error);
    }

    function unsubscribeToPushNotifications(){
        beamsClient.stop().then(() => {
            console.log('Beams client has stopped')
        }).catch(console.error)
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
        subscribeToPushNotifications()
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
        subscribeToPushNotifications()
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
        unsubscribeToPushNotifications()
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

    function getSettings(darkMode){
        return axios.get('/api/users/' + user.data.id + '/settings').then((response) =>{
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

    async function updateSettings(settings){
        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        }
        const response = await axios.put('/api/users/' + user.data.id + '/settings', settings, header)
        if(response.status === 200){
            if(response.data.data.profilePicture){
                console.log(response.data.data.profilePicture)
                user.data.profilePicture = response.data.data.profilePicture
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
        const response = await axios.put('/api/users/' + user.data.id + '/settings/update-distance-notifications-radius', {distanceNotificationsRadius: distanceNotificationsRadius}, header)
        if(response.status === 200){
            user.data.settings.distanceNotificationsRadius = distanceNotificationsRadius
            localStorage.setItem('userData', JSON.stringify(user.data))
        }
        return response
    }

    // TODO Move the Swal to the Notifications component

    function getNotifications(lastNotificationId, darkMode){
        if(lastNotificationId === null){
            lastNotificationId = ''
        }
        return axios.get('/api/users/' + user.data.id + '/notifications?lastNotificationId=' + lastNotificationId).then((response) =>{
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

    function getProfileInformation(userId, darkMode){
        return axios.get('/api/users/' + userId + '/profile').then((response) =>{
            return response.data
        }).catch((error) => {
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
            })
        })
    }

    function getProfileInformationByUsername(username, darkMode){
        return axios.get('/api/users/user-profile?username=' + username ).then((response) =>{
            return response.data
        }).catch((error) => {
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
            })
            if(error.response.status === 404){
                router.push('/')
            }
        })
    }
    async function updateProfileInformation(profileInformation){

        const header = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': profileInformation.profilePicture ? 'multipart/form-data' : 'application/json',
            },
        }

        profileInformation['_method'] = 'PUT'

        const response = await axios.post('/api/users/' + user.data.id + '/profile', profileInformation, header)
        if(response.status === 200) {
            if(response.data.data.profilePicture){
                user.data.profilePicture = response.data.data.profilePicture
                localStorage.setItem('userData', JSON.stringify(user.data))
            }
        }
        return response
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

    async function checkAuthentication(darkMode){
        return axios.get('/api/users/' + user.data.id + '/check-authentication').then((response) => {
            return true
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
            })
            if (error.response.status === 401) {
                router.push('/').then(t => {
                    return error.response
                })
            }
            return false
        })
    }




    return {
        user,
        notificationsCount,
        notificationsCountFetched,
        subscribeToNotifications,
        toast,
        login,
        register,
        logout,
        deleteAccount,
        forgotPassword,
        resetPassword,
        getSettings,
        updateSettings,
        updateDistanceNotificationsRadius,
        getNotifications,
        getProfileInformation,
        getProfileInformationByUsername,
        updateProfileInformation,
        checkAuthentication
    }
})
