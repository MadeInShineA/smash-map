<script>
import {defineComponent} from 'vue'

export default defineComponent({
    name: "App",

    mounted() {
        console.log('App Mounted')
    }
})
</script>

<template>
    <router-view></router-view>
</template>

<style scoped>

</style>
