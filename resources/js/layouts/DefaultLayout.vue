<template>
    <h1>Test</h1>
    <router-view></router-view>
    <div>
        <router-link to="/home">
            Go to Home
        </router-link>
    </div>

    <div>
        <router-link to="/map">
            Go to Map
        </router-link>
    </div>

</template>

<script>
export default {
    name: "DefaultLayout",

    mounted() {
        console.log('DefaultLayout Mounted')
    }
}
</script>

<style scoped>

</style>
