<script setup>
import {onMounted, ref, toRefs, watch} from "vue";
import {useUserStore} from "../stores/userStore";
import {defineProps} from "vue";
import Divider from 'primevue/divider';
import {useScroll} from '@vueuse/core'
import LoaderComponent from "@/components/LoaderComponent.vue";

const userStore = useUserStore()

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true
    }
})

const lastNotificationId = ref(null)
const fetchingNotifications = ref(true)

const notifications = ref([])

userStore.getNotifications(lastNotificationId.value, props.darkMode).then((response)=>{
    notifications.value = response.data.notifications
    lastNotificationId.value = response.data.lastNotificationId
    fetchingNotifications.value = false

})

const notificationsContainer = ref()
const { arrivedState: scrollStates} = useScroll(notificationsContainer, {
    offset: {bottom: 500}
})

const { bottom: hasScrolledDown } = toRefs(scrollStates)


watch(hasScrolledDown, function(isArrived){
    if(isArrived && !fetchingNotifications.value){
        fetchingNotifications.value = true
        userStore.getNotifications(lastNotificationId.value, props.darkMode).then((response)=>{
            notifications.value = notifications.value.concat(response.data.notifications)
            lastNotificationId.value = response.data.lastNotificationId
            fetchingNotifications.value = false
        })
    }
})

Echo.private(`notifications.` + userStore.user.data.id).listen('NotificationEvent', (notification) => {
    notifications.value.unshift(notification)
})


onMounted(()=>{
    console.log('Notifications Mounted')
})


</script>

<template>
    <div ref="notificationsContainer" v-if="notifications && notifications.length > 0" id="notifications-container" >
        <div v-for="notification in notifications" class="notification">
            <div class="notification-image-container">
                <img :src="notification.imageUrl" alt="Notification's image" width="50px">
            </div>
            <div class="notification-text-container">
                <h3>{{notification.notificationType}} for {{ notification.gameTitle}}</h3>
                <p v-html="notification.message" class="notification-message"></p>
            </div>
            <Divider />
        </div>
        <loader-component v-if="fetchingNotifications"></loader-component>
    </div>
    <div v-else-if="!fetchingNotifications && notifications && notifications.length === 0" id="notifications-container">
        <h1>No notifications</h1>
    </div>
    <LoaderComponent v-else></LoaderComponent>
</template>

<style scoped>
#notifications-container{
    height:100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    overflow-y:scroll;
}

.notification{
    width: 50%;
}

@media (max-width: 960px) {
    .notification{
        width: 100%
    }
}



.notification-text-container{
    text-align: center;
}

.notification-message :deep(a){
    color: inherit;
    text-decoration: none;
    font-weight: 600
}

.notification-image-container{
    display: flex;
    justify-content: center;
}


</style>
