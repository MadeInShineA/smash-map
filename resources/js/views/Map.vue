<script setup>

    import {onMounted} from "vue";

    onMounted(()=>{
        console.log('Map Mounted')
    })

</script>

<template>
    <GMapMap
        :zoom="7"
        map-type-id="terrain"
        style="width: 500px; height: 300px"
        >
    </GMapMap>
    <h1>Map</h1>
</template>

<style scoped>

</style>
