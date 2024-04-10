<script setup>
    import {onMounted} from "vue";
    import {useEventsStore} from "../stores/EventsStore.js";
    import MeterGroup from 'primevue/metergroup';


    const eventsStore = useEventsStore()
    eventsStore.fetchStatistics()




    onMounted(()=>{
        console.log('Home Mounted')
    })

</script>

<template>
    <template v-if="eventsStore.statisticsFetched">
        <MeterGroup :value="eventsStore.statistics.data"></MeterGroup>
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>


<style scoped>

</style>
