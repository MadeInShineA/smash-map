<template >
    <Qalendar :config="config" :events="events" />
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
                dayBoundaries: {
                    start: 8,
                    end: 22,
                },
            },
            events: [
                // ...
                {
                    title: "Advanced algebra",
                    with: "Chandler Bing",
                    time: { start: "2022-05-16 12:05", end: "2022-05-16 13:35" },
                    color: "yellow",
                    isEditable: true,
                    id: "753944708f0f",
                    description: "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores assumenda corporis doloremque et expedita molestias necessitatibus quam quas temporibus veritatis. Deserunt excepturi illum nobis perferendis praesentium repudiandae saepe sapiente voluptatem!"
                },
                {
                    title: "Ralph on holiday",
                    with: "Rachel Greene",
                    time: { start: "2022-05-10", end: "2022-05-22" },
                    color: "green",
                    isEditable: false,
                    id: "5602b6f589fc"
                }
                // ...
            ],
        }
    },

    mounted() {
        console.log('Calendar Mounted')
    }
}
</script>

<style>
@import "qalendar/dist/style.css";
</style>
