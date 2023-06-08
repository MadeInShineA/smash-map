<template >
    <Qalendar v-if="events.length > 0" :config="config" :events="events" />
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<script>
import { Qalendar } from "qalendar";
import LoaderComponent from "@/components/LoaderComponent.vue";

export default {
    components: {
        LoaderComponent,
        Qalendar,
    },

    data() {
        return {
            config: {
                locale: 'en-EN',
                defaultMode: 'month',
                showCurrentTime:true,
                isSilent: true,

            },
            style: {
                colorSchemes: {
                    online: {
                        color: '#fff',
                        backgroundColor: '#131313',
                    },
                    offline: {
                        color: '#fff',
                        backgroundColor: '#ff4081',
                    }
                }
            },
            events: [
            ],
        }
    },

    methods: {
        async fetchEvents() {
            try {
                const response = await axios.get('/api/calendar/events');
                const events = response.data.data;

                events.forEach(event => {
                    const convertedEvent = {
                        title: event.title,
                        time: { start: event.start, end: event.end },
                        location: event.location,
                        colorScheme: event.colorScheme,
                        description: event.description
                    };
                    this.events.push(convertedEvent);
                });

            } catch (error) {
                console.error(error);
            }
        },

    },

    created() {
        this.fetchEvents();
        console.log(this.events)
    },



    mounted() {

        console.log('Calendar Mounted')
    }
}
</script>

<style>
@import "qalendar/dist/style.css";
</style>
