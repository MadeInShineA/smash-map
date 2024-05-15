<script setup>

import {useUserStore} from "../stores/UserStore.js";
import {ref} from "vue";

const userStore = useUserStore()

const props = defineProps({
    darkMode: Boolean
})



const profileInformation = ref(null)
userStore.checkAuthentication(props.darkMode).then(response =>{
    console.log(response)
    if(response){
        userStore.getProfileInformation(userStore.user.data.id, props.darkMode).then((response)=> {
            profileInformation.value = response.data
        })
    }
})



</script>

<template>
    <h1>Profile</h1>
    <div v-if="profileInformation">
        <p>{{profileInformation}}
        </p>
    </div>

</template>

<style scoped>

</style>
