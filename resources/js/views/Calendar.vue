<template >
    <Qalendar v-if="events.length > 0" :config="config" :events="events" />
    <h1 v-else>Loading</h1>
</template>

<script>
import { Qalendar } from "qalendar";

export default {
    components: {
        Qalendar,
    },

    data() {
        return {
            config: {
                locale: 'en-EN',
                defaultMode: 'month',
                showCurrentTime:true,
                isSilent: true,
                // dayBoundaries: {
                //     start: 8,
                //     end: 22,
                // },
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
                    // {
                    //     title: "Advanced algebra",
                    //     with: "Chandler Bing",
                    //     time: { start: "2022-05-16 12:05", end: "2022-05-16 13:35" },
                    //     color: "yellow",
                    //     isEditable: true,
                    //     id: "753944708f0f",
                    //     description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                    // },
                    // {
                    //     title: "Ralph on holiday",
                    //     with: "Rachel Greene",
                    //     time: { start: "2022-05-10", end: "2022-05-22" },
                    //     color: "green",
                    //     isEditable: true,
                    //     id: "5602b6f589fc",
                    //     location:'<h1>Paris</h1>'
                    // }
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
                        // colorScheme: event.colorScheme,
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
