<script setup>
import {onMounted, ref} from "vue";
import {useUserStore} from "../stores/userStore";
import {defineProps} from "vue";
import Divider from 'primevue/divider';
import { useScroll } from '@vueuse/core'

const userStore = useUserStore()

const props = defineProps({
    darkMode: Boolean
})

const notifications = ref()
userStore.getNotifications(userStore.user.data.id, props.darkMode).then((response)=>{
    notifications.value = response.data
})

const notificationsContainer = ref()
const { x, y, isScrolling, arrivedState, directions } = useScroll(notificationsContainer)

onMounted(()=>{
    console.log('Notifications Mounted')
})
</script>

<template>
    <div ref="notificationsContainer" v-if="notifications && notifications.length > 0" id="notifications-container" >
        <div v-for="notification in notifications" class="notification">
            {{y}}
            {{isScrolling}}
            <div class="notification-image-container">
                <img :src="notification.imageUrl" alt="Notification's image" width="50px">
            </div>
            <div class="notification-text-container">
                <h3>{{notification.notificationType}} for {{ notification.gameTitle}}</h3>
                <p v-html="notification.message" class="notification-message"></p>
            </div>
            <Divider />
        </div>
    </div>
    <div v-else-if="notifications && notifications.length === 0" id="notifications-container">
        <h1>No notifications</h1>
    </div>
    <LoaderComponent v-else></LoaderComponent>
</template>

<style scoped>
#notifications-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 20px;
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
