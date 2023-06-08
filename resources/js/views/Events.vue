<script setup>

import {onMounted, ref} from "vue";

let events = ref({})

let loading = ref(true)

const fetchEvents = async function (page=1) {
    try {
        loading.value = true;
        const response = await axios.get('/api/events?page='+ page);
        events.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error(error);
    }
}

fetchEvents()

onMounted(()=>{
    console.log('Events Mounted')
})

</script>

<template>
    <template v-if="!loading">
        <div class="event-container">
            <Card v-for="event in events.data" :key="event.id" class="event-card">
                <template #header>
                    <div class="event-image-container">
                        <img v-if="event.images[0]" :src="event.images[0].url" alt="Event Image" class="event-image">
                        <img v-else src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png" alt="Event Image" class="event-image">
                    </div>
                </template>
                <template #title>
                    <a class="event-title" :href=event.link target="_blank"><i class="pi pi-external-link"/> {{ event.name }}</a>
                </template>
                <template #content>
                    <div v-if="!event.is_online" class="event-location"><Chip :label="event.address.name" icon="pi pi-map-marker"></Chip></div>
                    <div v-else class="event-location"><Chip label="Online" icon="pi pi-globe"></Chip></div>
                    <div v-if="event.attendees" class="event-attendees"><Chip :label=event.attendees icon="pi pi-users"></Chip></div>
                    <div class="event-datetime"><Chip :label="event.start_date_time + ' / ' + event.end_date_time + ' ' + event.timezone" icon="pi pi-clock"></Chip></div>
                </template>
            </Card>
        </div>
            <div class="pagination-container">
                <Bootstrap5Pagination :data="events" @pagination-change-page="fetchEvents"></Bootstrap5Pagination>
            </div>
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
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
    text-decoration: none;
    color: inherit;
}

.event-location {
    margin-bottom: 10px;
}

.event-attendees{
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
