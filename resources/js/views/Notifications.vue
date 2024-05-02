<script setup>
import {onMounted, ref} from "vue";
import {useUserStore} from "../stores/userStore";

const userStore = useUserStore()

const props = defineProps({
    darkMode: Boolean
})

const notifications = ref()
userStore.getNotifications(userStore.user.data.id, props.darkMode).then((response)=>{
    notifications.value = response.data
})

onMounted(()=>{
    console.log('Notifications Mounted')
})
</script>

<template>
    <div v-if="notifications">
        <div v-for="notification in notifications" :key="notification.id">
            <span>
                <img :src="notification.imageUrl" alt="Notification's icon"/>
            </span>
            <p  v-html="notification.message"></p>
        </div>
    </div>
    <LoaderComponent v-else></LoaderComponent>
</template>

<style scoped>

</style>
