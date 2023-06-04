<script setup>

import {onMounted, reactive} from "vue";

let events = reactive([])

    async function fetchEvents(page) {
        try {
            const response = await axios.get('/api/events?page='+ page);
            const fetchedEvents = response.data.data;

            fetchedEvents.forEach(event => {
                events.push(event);
            });

        } catch (error) {
            console.error(error);
        }
    }

    fetchEvents(1)

    onMounted(()=>{
        console.log('Events Mounted')
        console.log(events)
    })

</script>

<template>
    <div class="event-container" v-if="events.length > 0">
        <Card v-for="event in events" :key="event.id" class="event-card">
            <template #header>
                <div class="event-image-container">
                    <img v-if="event.images[0]" :src="event.images[0].url" alt="Event Image" class="event-image">
                    <img v-else src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png" alt="Event Image" class="event-image">
                </div>
            </template>
            <template #title class="event-title">
                <a :href=event.link target="_blank">{{ event.name }}</a>
            </template>
            <template #subtitle>
                <div v-if="!event.is_online" class="event-location"><Chip :label="event.address.name" icon="pi pi-map-marker"></Chip></div>
                <div v-else class="event-location"><Chip label="Online" icon="pi pi-globe"></Chip></div>
            </template>
            <template #content>
                <p class="event-datetime">{{ event.start_date_time }} - {{ event.end_date_time }} {{ event.timezone }}</p>
            </template>
        </Card>
    </div>
    <div v-else>
        <h1>Loading</h1>
    </div>
</template>

<!--TODO Double check the style and not forget responsive-->
<style scoped>

.event-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    justify-items: center;
    grid-gap: 20px;
    margin-left: 20px;
    margin-right: 20px;
}


.event-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    text-align: center;
    margin-top:15px;
    margin-bottom: 15px;
}

.event-image-container {
    display: flex;
    justify-content: center;
    margin-top:20px;
    margin-bottom: 10px;
}

.event-image {
    max-width: 100%;
    max-height: 200px;
}

.event-title {
    margin-bottom: 10px;
}

.event-location {
    margin-bottom: 10px;
}

.event-datetime {
    margin-bottom: 0;
}

@media (max-width: 960px) {
    .event-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 767px) {
    .event-container {
        grid-template-columns: 1fr;
    }
}

</style>
